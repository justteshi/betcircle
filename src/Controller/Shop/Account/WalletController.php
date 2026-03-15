<?php

declare(strict_types=1);

namespace App\Controller\Shop\Account;

use App\Entity\BetCircle\Wallet;
use App\Entity\BetCircle\WalletTransaction;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class WalletController extends AbstractController
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/account/wallet', name: 'app_shop_account_wallet', methods: ['GET'])]
    public function __invoke(): Response
    {
        $user = $this->security->getUser();

        if (!$user instanceof ShopUserInterface) {
            throw $this->createAccessDeniedException();
        }

        $customer = $user->getCustomer();

        if (null === $customer) {
            throw $this->createAccessDeniedException();
        }

        /** @var Wallet|null $wallet */
        $wallet = $this->entityManager
            ->getRepository(Wallet::class)
            ->findOneBy(['customer' => $customer]);

        $transactions = [];

        if (null !== $wallet) {
            $transactions = $this->entityManager
                ->getRepository(WalletTransaction::class)
                ->findBy(
                    ['wallet' => $wallet],
                    ['createdAt' => 'DESC', 'id' => 'DESC'],
                );
        }

        return $this->render('shop/account/wallet/index.html.twig', [
            'wallet' => $wallet,
            'transactions' => $transactions,
        ]);
    }
}
