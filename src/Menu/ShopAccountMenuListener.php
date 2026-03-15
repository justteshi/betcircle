<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class ShopAccountMenuListener
{
    public function addAccountMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $wallet = $menu
            ->addChild('wallet', [
                'route' => 'app_shop_account_wallet',
            ])
            ->setLabel('Wallet');

        $wallet->setLabelAttribute('icon', 'tabler:wallet');
    }
}
