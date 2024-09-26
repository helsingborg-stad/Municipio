<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class RemoveSubLevelDecorator implements MenuItemsDecoratorInterface
{
    /**
     * Removes sub level items
     *
     * @param   array   $menuItems    The unfiltered result set
     *
     * @return  array   $menuItems    The filtered result set (without sub levels)
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