<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

interface DefaultMenuItemDecoratorInterface
{
    /**
     * Decorates a menu item with default settings.
     *
     * @param array|object $menuItem The menu item to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param array $ancestors The ancestors of the menu item.
     * @return array The decorated menu item.
     */
    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig, array $ancestors): array;
}
