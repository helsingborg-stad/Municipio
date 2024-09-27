<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;

class AppendIsAncestorDecorator implements DefaultMenuItemDecoratorInterface
{
    /**
     * Decorates a menu item with the "ancestor" property based on its ID and the list of ancestors.
     *
     * @param array|object $menuItem The menu item to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param array $ancestors The list of ancestor menu item IDs.
     * @return array The decorated menu item.
     */
    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig, array $ancestors): array
    {
        if (!isset($menuItem['id']) || empty($ancestors)) {
            return $menuItem;
        }

        $menuItem['ancestor'] = in_array($menuItem['id'], $ancestors);

        return $menuItem;
    }
}