<?php

declare(strict_types=1);

namespace App\EventListener\BetCircle;

use App\Service\BetCircle\Wallet\WalletManager;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\GenericEvent;

#[AsEventListener(event: 'sylius.customer.post_create')]
final readonly class CreateWalletForCustomerListener
{
    public function __construct(
        private WalletManager $walletManager,
    ) {
    }

    public function __invoke(GenericEvent $event): void
    {
        $customer = $event->getSubject();

        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $this->walletManager->getOrCreateWallet($customer);
    }
}
