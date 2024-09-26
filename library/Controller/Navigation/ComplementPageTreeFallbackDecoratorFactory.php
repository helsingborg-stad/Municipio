<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\ComplementDecoratorFactoryInterface;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\ComplementPageTreeDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\TransformPageTreeFallbackMenuItemDecorator;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\AppendChildrenDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\AppendHrefDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\AppendIsAncestorPostDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\AppendIsCurrentPostDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\CustomTitleDecorator;
use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\ApplyMenuItemFilterDecorator;

class ComplementPageTreeFallbackDecoratorFactory implements ComplementDecoratorFactoryInterface
{
    private array $decorators;
    private GetAncestors $getAncestorsInstance;
    private GetPostsByParent $getPostsByParentInstance;
    private TransformPageTreeFallbackMenuItemDecorator $transformPageTreeFallbackMenuItemDecoratorInstance;

    public function __construct()
    {
        [
            $getAncestorsInstance, 
            $getPostsByParentInstance,
            $transformPageTreeFallbackMenuItemDecoratorInstance,
            $decorators
        ] = $this->getClassProperties();

         // Assign class properties
         $this->getAncestorsInstance = $getAncestorsInstance;
         $this->getPostsByParentInstance = $getPostsByParentInstance;
         $this->transformPageTreeFallbackMenuItemDecoratorInstance = $transformPageTreeFallbackMenuItemDecoratorInstance;
         $this->decorators = $decorators;
    }

    public static function createComplementDecoratorStatic(): MenuItemsDecoratorInterface
    {
        [
            $getAncestorsInstance, 
            $getPostsByParentInstance,
            $transformPageTreeFallbackMenuItemDecoratorInstance,
            $decorators
        ] = self::getClassProperties();

        return new ComplementPageTreeDecorator(
            $getAncestorsInstance,
            $getPostsByParentInstance,
            $transformPageTreeFallbackMenuItemDecoratorInstance,
            $decorators
        );
    }

    public function createComplementDecorator(): MenuItemsDecoratorInterface
    {
        return new ComplementPageTreeDecorator(
            $this->getAncestorsInstance,
            $this->getPostsByParentInstance,
            $this->transformPageTreeFallbackMenuItemDecoratorInstance,
            $this->decorators
        );
    }


    private function getClassProperties(): array
    {
        $getPageForPostTypeIdsInstance  = new GetPageForPostTypeIds();

        return [
            new GetAncestors($getPageForPostTypeIdsInstance), 
            new GetPostsByParent(new GetHiddenPostIds(), $getPageForPostTypeIdsInstance),
            new TransformPageTreeFallbackMenuItemDecorator(),
            $this->getDefaultDecorators()
        ];
    }

    public function getDecorators(): array
    {
        return $this->decorators;
    }

    public function setDecorators(array $decorators): void
    {
        $this->decorators = $decorators;
    }

    private function getDefaultDecorators(): array 
    {
        return [
            new AppendHrefDecorator(),
            new CustomTitleDecorator(),
            new AppendIsCurrentPostDecorator(),
            new AppendIsAncestorPostDecorator($this->getAncestorsInstance),
            new AppendChildrenDecorator($this->getPostsByParentInstance, new GetHiddenPostIds(), new GetPageForPostTypeIds()),
            new ApplyMenuItemFilterDecorator()
        ];
    }
}