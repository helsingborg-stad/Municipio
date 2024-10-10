<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\MenuInterface;

class PageTreeAppendMenuItemsAncestors implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    /*  
     * TODO: check if needed. Maybe we can build our own tree,
     * from the available ids of each menu item.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            if (!isset($menuItem['id']) || !empty($menuItem['isCached'])) {
                continue;
            }
            
            if (in_array($menuItem['id'], GetAncestors::getAncestors())) {
                $menuItem['ancestor'] = true;
            } else {
                $menuItem['ancestor'] = false;
            }
        }

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}