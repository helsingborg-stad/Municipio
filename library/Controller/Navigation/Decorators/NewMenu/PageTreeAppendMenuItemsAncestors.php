<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\NewMenuInterface;

class PageTreeAppendMenuItemsAncestors implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        if (empty($menuItems)) {
            return $menuItems;
        }

        foreach ($menuItems as &$menuItem) {
            if (!isset($menuItem['id']) || !empty($menuItem['isCached'])) {
                continue;
            }
            
            if (in_array($menuItem['id'], GetAncestors::getAncestors($this->getConfig()))) {
                $menuItem['ancestor'] = true;
            } else {
                $menuItem['ancestor'] = false;
            }
        }


        return $menuItems;
    }

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}