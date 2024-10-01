<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class StructureMenuItemsDecorator implements MenuItemsDecoratorInterface
{
    /**
     * Decorates the menu items based on the structure defined in the menu configuration.
     *
     * @param array $menuItems The original menu items.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @return array The decorated menu items.
     */
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        return $this->structuredMenuItems($menuItems);
    }

    /**
     * Generates a structured array of menu items based on the given menu items array and parent ID.
     *
     * @param array $menuItems The array of menu items.
     * @param int $parentId The parent ID to filter the menu items.
     * @return array The structured array of menu items.
     */
    private function structuredMenuItems(array $menuItems, int $parentId = 0): array
    {
        $structuredMenuItems = [];

        if (is_array($menuItems) && !empty($menuItems)) {
            foreach ($menuItems as $menuItem) {
                if (!isset($menuItem['post_parent']) || !isset($menuItem['id'])) {
                    continue;
                }

                if ($menuItem['post_parent'] == $parentId) {
                    $children = $this->structuredMenuItems($menuItems, $menuItem['id']);

                    if ($children) {
                        $menuItem['children'] = $children;
                    }

                    $structuredMenuItems[] = $menuItem;
                }
            }
        }

        return $structuredMenuItems;
    }
}
