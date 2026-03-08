<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\BetCircle\Wallet;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class WalletExtension extends AbstractExtension
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('wallet_balance', [$this, 'getWalletBalance']),
        ];
    }

    public function getWalletBalance(): int
    {
        $user = $this->security->getUser();

        if (!$user instanceof ShopUserInterface) {
            return 0;
        }

        $customer = $user->getCustomer();

        if (null === $customer) {
            return 0;
        }

        /** @var Wallet|null $wallet */
        $wallet = $this->entityManager
            ->getRepository(Wallet::class)
            ->findOneBy(['customer' => $customer]);

        if (null === $wallet) {
            return 0;
        }

        return $wallet->getBalance();
    }
}
