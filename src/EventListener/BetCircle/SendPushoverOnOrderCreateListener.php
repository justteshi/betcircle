<?php

declare(strict_types=1);

namespace App\EventListener\BetCircle;

use App\Service\Notification\PushoverNotifier;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsEventListener(event: 'sylius.order.post_complete')]
final readonly class SendPushoverOnOrderCreateListener
{
    public function __construct(
        private PushoverNotifier      $pushoverNotifier,
        private LoggerInterface       $logger,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        $subject = $event->getSubject();

        $this->logger->info('BetCircle order complete listener fired', [
            'subjectClass' => is_object($subject) ? $subject::class : gettype($subject),
        ]);

        if (!$subject instanceof OrderInterface) {
            $this->logger->warning('BetCircle Pushover skipped: subject is not an OrderInterface.');

            return;
        }

        $customer = $subject->getCustomer();

        $customerName = trim(sprintf(
            '%s %s',
            $customer?->getFirstName() ?? '',
            $customer?->getLastName() ?? '',
        ));

        if ('' === $customerName) {
            $customerName = $customer?->getEmail() ?? 'Unknown customer';
        }

        $total = number_format($subject->getTotal() / 100, 2);

        $message = sprintf(
            'New token order #%s from %s. Total: %s %s.',
            $subject->getNumber(),
            $customerName,
            $total,
            $subject->getCurrencyCode() ?? 'EUR',
        );

        // Verify the exact route name in your project with:
        // docker compose exec php bin/console debug:router | grep admin | grep order
        $adminOrderUrl = $this->urlGenerator->generate(
            'sylius_admin_order_show',
            ['id' => $subject->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $this->logger->info('BetCircle sending Pushover for order', [
            'orderId' => $subject->getId(),
            'orderNumber' => $subject->getNumber(),
            'customerName' => $customerName,
            'total' => $subject->getTotal(),
            'currencyCode' => $subject->getCurrencyCode(),
            'adminOrderUrl' => $adminOrderUrl,
        ]);

        $this->pushoverNotifier->send(
            'New BetCircle token order',
            $message,
            $adminOrderUrl,
            'Open order in admin',
        );
    }
}
