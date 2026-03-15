<?php

declare(strict_types=1);

namespace App\EventListener\BetCircle;

use App\Service\BetCircle\Wallet\OrderTokenCreditService;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Workflow\Event\Event;

#[AsEventListener(event: 'workflow.sylius_payment.completed.complete')]
final readonly class CreditWalletOnPaymentUpdateListener
{
    public function __construct(
        private OrderTokenCreditService $orderTokenCreditService,
        private LoggerInterface         $logger,
    ) {
    }

    public function __invoke(Event $event): void
    {
        $subject = $event->getSubject();

        if (!$subject instanceof PaymentInterface) {
            return;
        }

        $order = $subject->getOrder();

        if (null === $order) {
            return;
        }

        $this->logger->info('BetCircle payment completion listener fired', [
            'paymentId' => $subject->getId(),
            'paymentState' => $subject->getState(),
            'orderId' => $order->getId(),
            'orderNumber' => $order->getNumber(),
            'orderPaymentState' => $order->getPaymentState(),
        ]);

        $this->orderTokenCreditService->creditOrder($order);
    }
}
