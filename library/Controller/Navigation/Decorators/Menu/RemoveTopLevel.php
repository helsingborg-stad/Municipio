<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Remove top level
 */
class RemoveTopLevel implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner)
    {
    }

    /**
     * Retrieves the menu with removed top level.
     *
     * @return array The menu with removed top level.
     */
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
