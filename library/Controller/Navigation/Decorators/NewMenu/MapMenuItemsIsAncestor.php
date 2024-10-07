<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\NewMenuInterface;

class MapMenuItemsIsAncestor implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        $ancestors = GetAncestorIds::getAncestorIds($menuItems, $this->getConfig());

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

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}