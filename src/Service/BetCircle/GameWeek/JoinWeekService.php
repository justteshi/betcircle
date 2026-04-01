<?php

declare(strict_types=1);

namespace App\Service\BetCircle\GameWeek;

use App\Entity\BetCircle\GameWeek;
use App\Entity\BetCircle\WeekEntry;
use App\Enum\BetCircle\GameWeekStatus;
use App\Enum\BetCircle\WalletTransactionType;
use App\Service\BetCircle\Wallet\WalletDebitService;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final readonly class JoinWeekService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WalletDebitService     $walletDebitService,
    ) {
    }

    public function join(CustomerInterface $customer, GameWeek $gameWeek): WeekEntry
    {
        $this->assertGameWeekIsJoinable($gameWeek);
        $this->assertCustomerHasNotAlreadyJoined($customer, $gameWeek);

        $walletTransaction = $this->walletDebitService->debit(
            $customer,
            $gameWeek->getEntryCostTokens(),
            WalletTransactionType::WEEK_ENTRY,
            'game_week',
            (string) $gameWeek->getId(),
            sprintf('Join game week "%s"', $gameWeek->getName() ?? 'Unknown'),
            [
                'gameWeekId' => $gameWeek->getId(),
                'gameWeekName' => $gameWeek->getName(),
            ],
        );

        $weekEntry = new WeekEntry();
        $weekEntry->setCustomer($customer);
        $weekEntry->setGameWeek($gameWeek);
        $weekEntry->setWalletTransaction($walletTransaction);
        $weekEntry->setEntryCostTokens($gameWeek->getEntryCostTokens());
        $weekEntry->setWeeklyContributionTokens($gameWeek->getWeeklyPoolTokens());
        $weekEntry->setSeasonalContributionTokens($gameWeek->getSeasonalPoolContributionTokens());

        $this->entityManager->persist($weekEntry);
        $this->entityManager->flush();

        return $weekEntry;
    }

    private function assertGameWeekIsJoinable(GameWeek $gameWeek): void
    {
        if (GameWeekStatus::OPEN !== $gameWeek->getStatus()) {
            throw new \LogicException('This game week is not open for joining.');
        }

        if (!$gameWeek->isVisible()) {
            throw new \LogicException('This game week is not visible.');
        }

        $joinDeadlineAt = $gameWeek->getJoinDeadlineAt();

        if (null !== $joinDeadlineAt && new \DateTimeImmutable() > $joinDeadlineAt) {
            throw new \LogicException('The join deadline for this game week has passed.');
        }

        if (
            $gameWeek->getEntryCostTokens() !==
            $gameWeek->getWeeklyPoolTokens() + $gameWeek->getSeasonalPoolContributionTokens()
        ) {
            throw new \LogicException('This game week has an invalid token split configuration.');
        }
    }

    private function assertCustomerHasNotAlreadyJoined(CustomerInterface $customer, GameWeek $gameWeek): void
    {
        $existingEntry = $this->entityManager
            ->getRepository(WeekEntry::class)
            ->findOneBy([
                'customer' => $customer,
                'gameWeek' => $gameWeek,
            ]);

        if (null !== $existingEntry) {
            throw new \LogicException('You have already joined this game week.');
        }
    }
}
