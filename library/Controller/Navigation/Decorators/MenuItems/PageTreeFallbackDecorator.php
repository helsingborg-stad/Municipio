<?php

namespace Municipio\Controller\Navigation\Decorators\Menuitems;

use Municipio\Controller\Navigation\Decorators\GetAncestors;
use Municipio\Controller\Navigation\Decorators\GetPostsByParent;
use Municipio\Controller\Navigation\Decorators\MenuItems\ComplementPageTreeMenuItemsDecorator;

class PageTreeFallbackDecorator implements MenuItemsDecoratorInterface
{
    private $masterPostType = 'page';

    public function __construct(
        private int $pageId,
        private GetAncestors $getAncestorsInstance,
        private GetPostsByParent $getPostsByParentInstance,
        private ComplementPageTreeMenuItemsDecorator $complementPageTreeMenuItemsDecoratorInstance
    ) {
    }

    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        if (empty($menuItems) && $fallbackToPageTree && is_numeric($this->pageId)) {
            $menuItems = $this->getNested($includeTopLevel);
        }

        return $menuItems;
    }

    private function getNested(bool $includeTopLevel): array
    {
        //Get all ancestors
        $result = $this->getAncestorsInstance->getAncestors($includeTopLevel);

        //Get all parents
        $result = $this->getPostsByParentInstance->getPostsByParent($result, [$this->masterPostType, get_post_type()]);

        //Format response
        $result = $this->complementPageTreeMenuItemsDecoratorInstance->decorate($result);

        //Return
        return $result;
    }
}
