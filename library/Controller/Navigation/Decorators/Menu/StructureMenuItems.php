<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Structure menu items
 */
class StructureMenuItems implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner)
    {
    }

    /**
     * Retrieves the menu with structured menu items.
     *
     * @return array The menu with structured menu items.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (is_bool($this->getConfig()->getFallbackToPageTree())) {
            $menu['items'] = $this->structureMenuItems($menu['items']);
        }

        return $menu;
    }

    /**
     * Structures the menu items.
     *
     * @param array $menuItems The menu items.
     * @param int $parentId The parent ID.
     *
     * @return array The structured menu items.
     */
    private function structureMenuItems(array $menuItems, int $parentId = 0): array
    {
        $structuredMenuItems = [];
        if (is_array($menuItems) && !empty($menuItems)) {
            foreach ($menuItems as $menuItem) {
                if (!isset($menuItem['post_parent']) || !isset($menuItem['id'])) {
                    continue;
                }
                if ($menuItem['post_parent'] == $parentId) {
                    if ($menuItem['id'] != $menuItem['post_parent']) {
                        $children = $this->structureMenuItems($menuItems, $menuItem['id']);

                        if ($children) {
                            $menuItem['children'] = $children;
                        }
                    }

                    $structuredMenuItems[] = $menuItem;
                }
            }
        }

        return $structuredMenuItems;
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
