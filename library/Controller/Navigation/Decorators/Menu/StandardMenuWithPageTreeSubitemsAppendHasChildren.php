<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Append has children to non-bottom level menu items.
 */
class StandardMenuWithPageTreeSubitemsAppendHasChildren implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner)
    {
    }

    /**
     * Retrieves the menu with appended subitems indicating if they have children.
     *
     * @return array The modified menu array.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $bottomLevelIds = $this->getBottomLevelIds($menu['items']);
        
        foreach ($menu['items'] as &$menuItem) {
            if (!isset($bottomLevelIds[$menuItem['id']])) {
                $menuItem['children'] = true;
            }
        }

        return $menu;
    }

    /**
     * Retrieves the bottom level IDs from the given menu items.
     *
     * @param array $menuItems The array of menu items.
     * @return array The array of bottom level IDs.
     */
    private function getBottomLevelIds(array $menuItems): array
    {
        $menuItemIds = [];
        foreach ($menuItems as $menuItem) {
            $menuItemIds[$menuItem['id']] = $menuItem['id'];
        }

        foreach ($menuItems as $menuItem) {
            unset($menuItemIds[$menuItem['post_parent']]);
        }

        return $menuItemIds;
    }

    /**
     * Retrieves the configuration of the menu.
     *
     * @return MenuConfigInterface The configuration of the menu.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}