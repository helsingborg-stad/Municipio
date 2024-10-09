<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

class RemoveTopLevel implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (!$this->getConfig()->getRemoveTopLevel()) {
            return $menu;
        }

        $menuItems = [];
        foreach ($menu['items'] as $menuItem) {
            if ($menuItem['ancestor'] == true && is_array($menuItem['children'])) {
                $menuItems = $menuItem['children'];
            }
        }

        $menu['items'] = $menuItems;
        
        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}