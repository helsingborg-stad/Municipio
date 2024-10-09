<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;
use Municipio\Controller\Navigation\MenuInterface;

class MapMenuItemsIsAncestor implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $ancestors = GetAncestorIds::getAncestorIds($menu['items'], $this->getConfig()->getIdentifier());

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            if (!isset($menuItem['id']) || empty($ancestors)) {
                continue;
            }
    
            $menuItem['ancestor'] = in_array($menuItem['id'], $ancestors);
        }


        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}