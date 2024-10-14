<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Convert static menu items to page tree items
 */
class ConvertStaticMenuItemsToPageTreeItems implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner)
    {
    }

    /**
     * Retrieves the menu with converted static menu items to page tree items.
     *
     * @return array The menu with converted static menu items to page tree items.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            $menuItem['active']   = null;
            $menuItem['ancestor'] = null;
            $menuItem['children'] = null;
            $menuItem['id']       = $menuItem['page_id'] ? (int) $menuItem['page_id'] : ($menuItem['id'] ?? null);
        }

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
