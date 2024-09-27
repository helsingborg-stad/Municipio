<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class ApplyMenuItemFilterDecorator implements DefaultMenuItemDecoratorInterface
{
    /**
     * Decorates a menu item with the ApplyMenuItemFilterDecorator.
     *
     * @param array|object $menuItem The menu item to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param array $ancestors The ancestors of the menu item.
     * @return array The decorated menu item.
     */
    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig, array $ancestors): array
    {
        return apply_filters('Municipio/Navigation/Item', $menuItem, $menuConfig->getIdentifier(), true);
    }
}