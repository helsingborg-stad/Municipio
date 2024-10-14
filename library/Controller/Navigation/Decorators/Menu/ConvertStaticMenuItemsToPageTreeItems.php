<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

class ConvertStaticMenuItemsToPageTreeItems implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            $menuItem['active']   = null;
            $menuItem['ancestor'] = null;
            $menuItem['children'] = null;
            $menuItem['id']       = $menuItem['page_id'] ? (int) $menuItem['page_id'] : ($menuItem['id'] ?? null);
        }

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
