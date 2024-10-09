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

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $menu['items'] = GetMenuData::getNavMenuItems($this->getConfig()->getMenuName()) ?: [];

        return $menu;
    }
    
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}