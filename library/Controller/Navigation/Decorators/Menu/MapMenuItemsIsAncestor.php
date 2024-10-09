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

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        $ancestors = GetAncestorIds::getAncestorIds($menuItems, $this->getConfig()->getIdentifier());

        if (empty($menuItems)) {
            return $menuItems;
        }

        foreach ($menuItems as &$menuItem) {
            if (!isset($menuItem['id']) || empty($ancestors)) {
                continue;
            }
    
            $menuItem['ancestor'] = in_array($menuItem['id'], $ancestors);
        }


        return $menuItems;
    }

    public function getMenu(): array
    {
        return $this->inner->getMenu();
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}