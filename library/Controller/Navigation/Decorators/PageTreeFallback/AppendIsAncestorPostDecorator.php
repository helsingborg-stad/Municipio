<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;

class AppendIsAncestorPostDecorator implements PageTreeFallbackMenuItemDecoratorInterface
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
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig): array
    {
        if (in_array($menuItem['id'], $this->getAncestorsInstance->getAncestors($menuConfig))) {
            $menuItem['ancestor'] = true;
        } else {
            $menuItem['ancestor'] = false;
        }

        return $menuItem;
    }
}