<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class ApplyNavigationItemsFilterDecorator implements MenuItemsDecoratorInterface
{
    /**
     * Decorates an array of menu items with a filter.
     *
     * This method applies a filter to an array of menu items, allowing customization of the menu items.
     *
     * @param array $menuItems The array of menu items to be decorated.
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @return array The decorated array of menu items.
     */
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        return apply_filters('Municipio/Navigation/Items', $menuItems, $menuConfig->getIdentifier());
    }
}