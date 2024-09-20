<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem;

use Municipio\Controller\Navigation\Decorators\GetAncestors;

class AppendIsAncestorPostDecorator implements MenuItemDecoratorInterface
{
    public function __construct(
        private GetAncestors $getAncestorsInstance
    ) {
    }
    /**
     * Add post is ancestor data on post array
     *
     * @param   object   $array         The post array
     *
     * @return  array    $postArray     The post array, with appended data
     */
    public function decorate(array $menuItem): array
    {
        if (in_array($menuItem['ID'], $this->getAncestorsInstance->getAncestors())) {
            $menuItem['ancestor'] = true;
        } else {
            $menuItem['ancestor'] = false;
        }

        return $menuItem;
    }
}