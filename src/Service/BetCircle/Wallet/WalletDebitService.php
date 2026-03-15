<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Wallet;

use App\Entity\BetCircle\WalletTransaction;
use App\Enum\BetCircle\WalletTransactionDirection;
use App\Enum\BetCircle\WalletTransactionType;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final readonly class WalletDebitService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private WalletManager          $walletManager,
    ) {
    }

    public function debit(
        CustomerInterface $customer,
        int $amount,
        WalletTransactionType $type,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?string $description = null,
        ?array $metadata = null,
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Debit amount must be greater than zero.');
        }

        $wallet = $this->walletManager->getOrCreateWallet($customer);

        $balanceBefore = $wallet->getBalance();

        if ($balanceBefore < $amount) {
            throw new \LogicException('Insufficient wallet balance.');
        }

        $balanceAfter = $balanceBefore - $amount;

        $wallet->setBalance($balanceAfter);

        $transaction = new WalletTransaction();
        $transaction->setWallet($wallet);
        $transaction->setCustomer($customer);
        $transaction->setType($type);
        $transaction->setDirection(WalletTransactionDirection::DEBIT);
        $transaction->setAmount($amount);
        $transaction->setBalanceBefore($balanceBefore);
        $transaction->setBalanceAfter($balanceAfter);
        $transaction->setReferenceType($referenceType);
        $transaction->setReferenceId($referenceId);
        $transaction->setDescription($description);
        $transaction->setMetadata($metadata);

        $this->entityManager->persist($wallet);
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $transaction;
    }
}
