<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItems\Default;

use Municipio\Controller\Navigation\Decorators\MenuItems\MenuItemsDecoratorInterface;

class RemoveSubLevelDecorator implements MenuItemsDecoratorInterface
{
    /**
     * Removes sub level items
     *
     * @param   array   $menuItems    The unfiltered result set
     *
     * @return  array   $menuItems    The filtered result set (without sub levels)
     */
    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        if ($onlyKeepFirstLevel) {
            foreach ($menuItems as $key => $item) {
                $menuItems[$key]['children'] = false;
            }
        }

        return $menuItems;
    }

}