<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\Navigation\GetMenuData;

class AppendMenuItems implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        $menuItems = GetMenuData::getNavMenuItems($this->getConfig()->getMenuName()) ?: [];

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