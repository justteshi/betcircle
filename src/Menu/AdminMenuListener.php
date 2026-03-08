<?php

declare(strict_types=1);

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        // Remove Sylius sections you don't want
        $menu->removeChild('official_support');
        $menu->removeChild('sylius.ui.administration');

        $betCircleSubmenu = $menu
            ->addChild('betcircle')
            ->setLabel('app.ui.betcircle')
            ->setLabelAttribute('icon', 'tabler:trophy')
        ;

        $betCircleSubmenu
            ->addChild('leagues', ['route' => 'app_admin_league_index'])
            ->setLabel('app.ui.leagues')
        ;

        $betCircleSubmenu
            ->addChild('teams', ['route' => 'app_admin_team_index'])
            ->setLabel('app.ui.teams')
        ;

        $betCircleSubmenu
            ->addChild('seasons', ['route' => 'app_admin_season_index'])
            ->setLabel('app.ui.seasons')
        ;

        $betCircleSubmenu
            ->addChild('game_weeks', ['route' => 'app_admin_game_week_index'])
            ->setLabel('app.ui.game_weeks')
        ;
        $betCircleSubmenu
            ->addChild('fixtures', ['route' => 'app_admin_fixture_index'])
            ->setLabel('app.ui.fixtures')
        ;

        $childrenOrder = array_keys($menu->getChildren());
        $betcircleIndex = array_search('betcircle', $childrenOrder, true);
        $catalogIndex = array_search('catalog', $childrenOrder, true);

        if (false !== $betcircleIndex && false !== $catalogIndex) {
            unset($childrenOrder[$betcircleIndex]);
            $childrenOrder = array_values($childrenOrder);

            array_splice($childrenOrder, $catalogIndex + 1, 0, ['betcircle']);

            $menu->reorderChildren($childrenOrder);
        }

        foreach ($menu->getChildren() as $key => $child) {
            $child->setExtra('always_open', $key === 'betcircle');
        }
    }
}
