<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class RemoveTopLevelDecorator implements MenuItemsDecoratorInterface
{
    /**
     * Decorates the menu items by removing the top level if specified in the menu configuration.
     *
     * @param array $menuItems The array of menu items to be decorated.
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @return array The decorated array of menu items.
     */
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        if ($menuConfig->getIncludeTopLevel()) {
            return $menuItems;
        }

        foreach ($menuItems as $menuItem) {
            if ($menuItem['ancestor'] == true && is_array($menuItem['children'])) {
                return $menuItem['children'];
            }
        }

        return [];
    }
}