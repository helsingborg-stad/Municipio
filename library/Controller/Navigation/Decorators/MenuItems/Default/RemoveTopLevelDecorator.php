<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItems\Default;

use Municipio\Controller\Navigation\Decorators\MenuItems\MenuItemsDecoratorInterface;

class RemoveTopLevelDecorator implements MenuItemsDecoratorInterface
{
    /**
     * Removes top level items
     *
     * @param   array   $menuItems    The unfiltered menuItems set
     *
     */
    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        if ($includeTopLevel) {
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