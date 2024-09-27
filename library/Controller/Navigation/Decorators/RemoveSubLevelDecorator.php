<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class RemoveSubLevelDecorator implements MenuItemsDecoratorInterface
{
    /**
     * Decorates the menu items by removing sub-levels based on the menu configuration.
     *
     * @param array $menuItems The array of menu items to be decorated.
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @return array The decorated array of menu items.
     */
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        if ($menuConfig->getOnlyKeepFirstLevel()) {
            foreach ($menuItems as $key => $item) {
                $menuItems[$key]['children'] = false;
            }
        }

        return $menuItems;
    }
}