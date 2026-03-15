<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Wallet;

use App\Entity\BetCircle\WalletTransaction;
use App\Enum\BetCircle\WalletTransactionType;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final readonly class OrderTokenCreditService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TokenAmountResolver    $tokenAmountResolver,
        private WalletCreditService    $walletCreditService,
    ) {
    }

    public function creditOrder(OrderInterface $order): ?WalletTransaction
    {
        $customer = $order->getCustomer();
        if (null === $customer) {
            return null;
        }

        if (!$this->canCreditOrder($order)) {
            return null;
        }

        $tokenAmount = 0;

        foreach ($order->getItems() as $orderItem) {
            $tokenAmount += $this->tokenAmountResolver->resolveOrderItemTokens($orderItem);
        }

        if ($tokenAmount <= 0) {
            return null;
        }

        return $this->walletCreditService->credit(
            $customer,
            $tokenAmount,
            WalletTransactionType::TOKEN_PURCHASE,
            'order',
            (string) $order->getId(),
            sprintf('Token purchase from order #%s', $order->getNumber()),
            [
                'orderId' => $order->getId(),
                'orderNumber' => $order->getNumber(),
                'tokenAmount' => $tokenAmount,
            ],
        );
    }

    public function canCreditOrder(OrderInterface $order): bool
    {
        $existingTransaction = $this->entityManager
            ->getRepository(WalletTransaction::class)
            ->findOneBy([
                'type' => WalletTransactionType::TOKEN_PURCHASE,
                'referenceType' => 'order',
                'referenceId' => (string) $order->getId(),
            ]);

        return null === $existingTransaction;
    }
}
