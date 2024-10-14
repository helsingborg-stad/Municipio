<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\Navigation\GetMenuData;

/**
 * Append menu items
 */
class AppendMenuItems implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner)
    {
    }

    /**
     * Retrieves the menu with appended menu items.
     *
     * @return array The menu with appended menu items.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $menu['items'] = GetMenuData::getNavMenuItems($this->getConfig()->getMenuName()) ?: [];

        return $menu;
    }

    /**
     * Retrieves the menu configuration.
     *
     * @return MenuConfigInterface The menu configuration.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
