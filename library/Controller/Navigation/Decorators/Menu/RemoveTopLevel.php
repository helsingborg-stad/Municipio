<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

class RemoveTopLevel implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        if (!$this->getConfig()->getRemoveTopLevel()) {
            return $menuItems;
        }

        foreach ($menuItems as $menuItem) {
            if ($menuItem['ancestor'] == true && is_array($menuItem['children'])) {
                return $menuItem['children'];
            }
        }

        return [];
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}