<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Prediction;

use App\Entity\BetCircle\Fixture;
use App\Entity\BetCircle\GameWeek;
use App\Entity\BetCircle\Prediction;
use App\Entity\BetCircle\WeekEntry;
use App\Service\BetCircle\Scoring\PredictionOutcomeResolver;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final readonly class PredictionSubmissionService
{
    public function __construct(
        private EntityManagerInterface    $entityManager,
        private PredictionOutcomeResolver $predictionOutcomeResolver,
    ) {
    }

    /**
     * @param array<int, array{
     *     fixtureId:int|string,
     *     predictedHomeScore:int|string,
     *     predictedAwayScore:int|string
     * }> $predictionsData
     *
     * @return array<int, Prediction>
     */
    public function submit(CustomerInterface $customer, GameWeek $gameWeek, array $predictionsData): array
    {
        $this->assertPredictionWindowIsOpen($gameWeek);

        $weekEntry = $this->getWeekEntry($customer, $gameWeek);

        $savedPredictions = [];

        foreach ($predictionsData as $predictionData) {
            $fixtureId = isset($predictionData['fixtureId']) ? (int) $predictionData['fixtureId'] : 0;
            $predictedHomeScore = isset($predictionData['predictedHomeScore']) ? (int) $predictionData['predictedHomeScore'] : 0;
            $predictedAwayScore = isset($predictionData['predictedAwayScore']) ? (int) $predictionData['predictedAwayScore'] : 0;

            if ($fixtureId <= 0) {
                throw new \InvalidArgumentException('Fixture id is required.');
            }

            if ($predictedHomeScore < 0 || $predictedAwayScore < 0) {
                throw new \InvalidArgumentException('Predicted scores must be zero or greater.');
            }

            $fixture = $this->getFixture($fixtureId);

            if ($fixture->getGameWeek()?->getId() !== $gameWeek->getId()) {
                throw new \LogicException('Fixture does not belong to this game week.');
            }

            /** @var Prediction|null $prediction */
            $prediction = $this->entityManager
                ->getRepository(Prediction::class)
                ->findOneBy([
                    'customer' => $customer,
                    'fixture' => $fixture,
                ]);

            if (null === $prediction) {
                $prediction = new Prediction();
                $prediction->setCustomer($customer);
                $prediction->setGameWeek($gameWeek);
                $prediction->setWeekEntry($weekEntry);
                $prediction->setFixture($fixture);
            }

            $prediction->setPredictedHomeScore($predictedHomeScore);
            $prediction->setPredictedAwayScore($predictedAwayScore);
            $prediction->setPredictedOutcome(
                $this->predictionOutcomeResolver->resolve($predictedHomeScore, $predictedAwayScore)
            );

            // If predictions are edited before lock, they must be re-scored later.
            $prediction->setAwardedPoints(0);
            $prediction->setIsScored(false);

            $this->entityManager->persist($prediction);
            $savedPredictions[] = $prediction;
        }

        $this->entityManager->flush();

        return $savedPredictions;
    }

    private function assertPredictionWindowIsOpen(GameWeek $gameWeek): void
    {
        $predictionLockAt = $gameWeek->getPredictionLockAt();

        if (null !== $predictionLockAt && new \DateTimeImmutable() > $predictionLockAt) {
            throw new \LogicException('Predictions are locked for this game week.');
        }
    }

    private function getWeekEntry(CustomerInterface $customer, GameWeek $gameWeek): WeekEntry
    {
        /** @var WeekEntry|null $weekEntry */
        $weekEntry = $this->entityManager
            ->getRepository(WeekEntry::class)
            ->findOneBy([
                'customer' => $customer,
                'gameWeek' => $gameWeek,
            ]);

        if (null === $weekEntry) {
            throw new \LogicException('You must join this game week before submitting predictions.');
        }

        return $weekEntry;
    }

    private function getFixture(int $fixtureId): Fixture
    {
        /** @var Fixture|null $fixture */
        $fixture = $this->entityManager
            ->getRepository(Fixture::class)
            ->find($fixtureId);

        if (null === $fixture) {
            throw new \InvalidArgumentException(sprintf('Fixture with id %d was not found.', $fixtureId));
        }

        return $fixture;
    }
}
