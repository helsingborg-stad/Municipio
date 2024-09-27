<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

interface MenuItemsDecoratorInterface
{
    /**
     * Decorates an array of menu items based on a menu configuration.
     *
     * @param array $menuItems The array of menu items to be decorated.
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @return array The decorated array of menu items.
     */
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array;
}
