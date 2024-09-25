<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem\PageTreeFallback;

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
    public function decorate(array $menuItem, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        if (in_array($menuItem['id'], $this->getAncestorsInstance->getAncestors())) {
            $menuItem['ancestor'] = true;
        } else {
            $menuItem['ancestor'] = false;
        }

        return $menuItem;
    }
}