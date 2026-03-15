<?php

declare(strict_types=1);

namespace App\Service\BetCircle\Wallet;

use App\Entity\BetCircle\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final readonly class WalletManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getOrCreateWallet(CustomerInterface $customer): Wallet
    {
        /** @var Wallet|null $wallet */
        $wallet = $this->entityManager
            ->getRepository(Wallet::class)
            ->findOneBy(['customer' => $customer]);

        if (null !== $wallet) {
            return $wallet;
        }

        $wallet = new Wallet();
        $wallet->setCustomer($customer);
        $wallet->setBalance(0);

        $this->entityManager->persist($wallet);
        $this->entityManager->flush();

        return $wallet;
    }
}
