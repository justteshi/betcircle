<?php

declare(strict_types=1);

namespace App\Service\BetCircle\GameWeek;

use App\Entity\BetCircle\Fixture;
use App\Entity\BetCircle\GameWeek;
use App\Entity\BetCircle\Prediction;
use App\Entity\BetCircle\PrizePayout;
use App\Entity\BetCircle\PrizePoolSnapshot;
use App\Entity\BetCircle\StandingSnapshot;
use App\Entity\BetCircle\WeekEntry;
use App\Enum\BetCircle\FixtureStatus;
use App\Enum\BetCircle\PrizePayoutStatus;
use App\Enum\BetCircle\PrizePayoutType;
use App\Enum\BetCircle\StandingSnapshotType;
use App\Service\BetCircle\Payout\PrizeSplitService;
use App\Service\BetCircle\Scoring\PredictionScoringService;
use Doctrine\ORM\EntityManagerInterface;

final readonly class WeekFinalizationService
{
    public function __construct(
        private EntityManagerInterface   $entityManager,
        private PredictionScoringService $predictionScoringService,
        private PrizeSplitService        $prizeSplitService,
    ) {
    }

    public function finalize(GameWeek $gameWeek): void
    {
        if ($gameWeek->isFinalized()) {
            throw new \LogicException('This game week is already finalized.');
        }

        $fixtures = $gameWeek->getFixtures()->toArray();

        if ([] === $fixtures) {
            throw new \LogicException('Cannot finalize a game week without fixtures.');
        }

        foreach ($fixtures as $fixture) {
            if (!$fixture instanceof Fixture) {
                continue;
            }

            if (FixtureStatus::FINISHED !== $fixture->getStatus()) {
                throw new \LogicException('All fixtures must be finished before finalization.');
            }

            $this->predictionScoringService->scoreFixturePredictions($fixture);
        }

        $leaderboard = $this->buildLeaderboard($gameWeek);
        $entryCount = $this->countWeekEntries($gameWeek);

        $weeklyPoolTokens = $this->sumWeekWeeklyPoolTokens($gameWeek);
        $seasonalPoolTokens = $this->sumSeasonalPoolTokensForSeason($gameWeek);

        $this->createPrizePoolSnapshot(
            $gameWeek,
            $weeklyPoolTokens,
            $seasonalPoolTokens,
            $entryCount,
        );

        $winnerRows = $this->getWinnerRows($leaderboard);

        $winnerShares = [];
        if ([] !== $winnerRows && $weeklyPoolTokens > 0) {
            $winnerShares = $this->prizeSplitService->splitEqually($weeklyPoolTokens, count($winnerRows));
        }

        $winnerCustomerIdToPrize = [];
        foreach ($winnerRows as $index => $winnerRow) {
            $winnerCustomerIdToPrize[$winnerRow['customerId']] = $winnerShares[$index] ?? 0;
        }

        foreach ($leaderboard as $row) {
            $prizeTokens = $winnerCustomerIdToPrize[$row['customerId']] ?? 0;
            $isWinner = $prizeTokens > 0;

            $standingSnapshot = new StandingSnapshot();
            $standingSnapshot->setType(StandingSnapshotType::WEEKLY);
            $standingSnapshot->setSeason($gameWeek->getSeason());
            $standingSnapshot->setGameWeek($gameWeek);
            $standingSnapshot->setCustomer($row['customer']);
            $standingSnapshot->setRank($row['rank']);
            $standingSnapshot->setPoints($row['points']);
            $standingSnapshot->setPrizeTokens($prizeTokens);
            $standingSnapshot->setWinner($isWinner);

            $this->entityManager->persist($standingSnapshot);

            if ($isWinner) {
                $prizePayout = new PrizePayout();
                $prizePayout->setCustomer($row['customer']);
                $prizePayout->setSeason($gameWeek->getSeason());
                $prizePayout->setGameWeek($gameWeek);
                $prizePayout->setType(PrizePayoutType::WEEKLY);
                $prizePayout->setAmountTokens($prizeTokens);
                $prizePayout->setStatus(PrizePayoutStatus::AVAILABLE);

                $this->entityManager->persist($prizePayout);
            }
        }

        if (method_exists($gameWeek, 'setFinalized')) {
            $gameWeek->setFinalized(true);
        } else {
            throw new \LogicException('GameWeek is missing setFinalized() method.');
        }

        if (method_exists($gameWeek, 'setFinalizedAt')) {
            $gameWeek->setFinalizedAt(new \DateTimeImmutable());
        }

        $this->entityManager->persist($gameWeek);
        $this->entityManager->flush();
    }

    /**
     * @return array<int, array{
     *     customerId:int,
     *     customer:object,
     *     points:int,
     *     rank:int
     * }>
     */
    private function buildLeaderboard(GameWeek $gameWeek): array
    {
        /** @var Prediction[] $predictions */
        $predictions = $this->entityManager
            ->getRepository(Prediction::class)
            ->findBy(['gameWeek' => $gameWeek], ['id' => 'ASC']);

        $totals = [];

        foreach ($predictions as $prediction) {
            $customer = $prediction->getCustomer();

            if (null === $customer || null === $customer->getId()) {
                continue;
            }

            $customerId = $customer->getId();

            if (!isset($totals[$customerId])) {
                $totals[$customerId] = [
                    'customerId' => $customerId,
                    'customer' => $customer,
                    'points' => 0,
                ];
            }

            $totals[$customerId]['points'] += $prediction->getAwardedPoints();
        }

        $rows = array_values($totals);

        usort(
            $rows,
            static function (array $left, array $right): int {
                if ($left['points'] === $right['points']) {
                    return $left['customerId'] <=> $right['customerId'];
                }

                return $right['points'] <=> $left['points'];
            }
        );

        $currentRank = 0;
        $previousPoints = null;

        foreach ($rows as $index => $row) {
            if (null === $previousPoints || $row['points'] < $previousPoints) {
                $currentRank = $index + 1;
            }

            $rows[$index]['rank'] = $currentRank;
            $previousPoints = $row['points'];
        }

        return $rows;
    }

    /**
     * @param array<int, array{
     *     customerId:int,
     *     customer:object,
     *     points:int,
     *     rank:int
     * }> $leaderboard
     *
     * @return array<int, array{
     *     customerId:int,
     *     customer:object,
     *     points:int,
     *     rank:int
     * }>
     */
    private function getWinnerRows(array $leaderboard): array
    {
        if ([] === $leaderboard) {
            return [];
        }

        $topRank = $leaderboard[0]['rank'];

        return array_values(array_filter(
            $leaderboard,
            static fn (array $row): bool => $row['rank'] === $topRank
        ));
    }

    private function createPrizePoolSnapshot(
        GameWeek $gameWeek,
        int $weeklyPoolTokens,
        int $seasonalPoolTokens,
        int $entryCount,
    ): void {
        $snapshot = new PrizePoolSnapshot();
        $snapshot->setSeason($gameWeek->getSeason());
        $snapshot->setGameWeek($gameWeek);
        $snapshot->setWeeklyPoolTokens($weeklyPoolTokens);
        $snapshot->setSeasonalPoolTokens($seasonalPoolTokens);
        $snapshot->setEntryCount($entryCount);

        $this->entityManager->persist($snapshot);
    }

    private function countWeekEntries(GameWeek $gameWeek): int
    {
        /** @var WeekEntry[] $entries */
        $entries = $this->entityManager
            ->getRepository(WeekEntry::class)
            ->findBy(['gameWeek' => $gameWeek]);

        return count($entries);
    }

    private function sumWeekWeeklyPoolTokens(GameWeek $gameWeek): int
    {
        /** @var WeekEntry[] $entries */
        $entries = $this->entityManager
            ->getRepository(WeekEntry::class)
            ->findBy(['gameWeek' => $gameWeek]);

        $total = 0;

        foreach ($entries as $entry) {
            $total += $entry->getWeeklyContributionTokens();
        }

        return $total;
    }

    private function sumSeasonalPoolTokensForSeason(GameWeek $gameWeek): int
    {
        $season = $gameWeek->getSeason();

        if (null === $season) {
            return 0;
        }

        /** @var WeekEntry[] $entries */
        $entries = $this->entityManager
            ->getRepository(WeekEntry::class)
            ->createQueryBuilder('we')
            ->innerJoin('we.gameWeek', 'gw')
            ->andWhere('gw.season = :season')
            ->setParameter('season', $season)
            ->getQuery()
            ->getResult();

        $total = 0;

        foreach ($entries as $entry) {
            $total += $entry->getSeasonalContributionTokens();
        }

        return $total;
    }
}
