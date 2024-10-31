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

        $mappedParentIdToPageId = [];

        foreach ($menu['items'] as &$menuItem) {
            if ($menuItem['page_id']) {
                $mappedParentIdToPageId[$menuItem['id']] = $menuItem['page_id'];
            }

            $menuItem['active']      = null;
            $menuItem['children']    = null;
            $menuItem['id']          = $menuItem['page_id'] ? (int) $menuItem['page_id'] : ($menuItem['id'] ?? null);
            $menuItem['post_parent'] = isset($mappedParentIdToPageId[$menuItem['post_parent']]) ? $mappedParentIdToPageId[$menuItem['post_parent']] : $menuItem['post_parent'];
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
