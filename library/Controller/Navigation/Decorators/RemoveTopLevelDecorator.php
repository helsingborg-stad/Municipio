<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class RemoveTopLevelDecorator implements MenuItemsDecoratorInterface
{
    /**
     * Removes top level items
     *
     * @param   array   $menuItems    The unfiltered menuItems set
     *
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