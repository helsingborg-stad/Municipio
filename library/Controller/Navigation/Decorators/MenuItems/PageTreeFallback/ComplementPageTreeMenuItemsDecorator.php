<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItems\PageTreeFallback;

use Municipio\Controller\Navigation\Cache\RuntimeCache;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\Decorators\MenuItems\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Decorators\MenuItem\PageTreeFallback\TransformObjectDecorator;
use Municipio\Controller\Navigation\Decorators\MenuItems\PageTreeFallbackMenuItemsDecoratorInterface;

class ComplementPageTreeMenuItemsDecorator implements MenuItemsDecoratorInterface
{
    private $masterPostType = 'page';

    public function __construct(
        private string $identifier,
        private int $pageId,
        private RuntimeCache $runtimeCache,
        private GetAncestors $getAncestorsInstance,
        private GetPostsByParent $getPostsByParentInstance,
        private TransformObjectDecorator $transformObjectDecoratorInstance,
        private array $menuItemDecorators
    ) {}

    /**
     * Calculate add add data to array
     *
     * @param   array    $menuItems     The post array
     *
     * @return  array    $menuItems     The post array, with appended data
     */
    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        if (!empty($menuItems) || !$fallbackToPageTree || !is_numeric($this->pageId)) {
            return $menuItems;
        }

        // Get all ancestors
        $menuItems = $this->getAncestorsInstance->getAncestors($includeTopLevel);

        // Get all parents
        $menuItems = $this->getPostsByParentInstance->getPostsByParent($menuItems, [$this->masterPostType, get_post_type()]);

        if (is_array($menuItems) && !empty($menuItems)) {
            foreach ($menuItems as $key => $menuItem) {
                // Generate a unique cache key for each item
                $cacheKey = md5(serialize($menuItem));

                // Check if the item is already in the cache
                $cacheData = $this->runtimeCache->getCache('complementObjects');

                if (!isset($cacheData[$cacheKey])) {
                    // Structures the menu item as the first step
                    $menuItem = $this->transformObjectDecoratorInstance->decorate($menuItem, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);

                    foreach ($this->menuItemDecorators as $decorator) {
                        $menuItem = $decorator->decorate($menuItem, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);
                    }

                    // Store the processed item in the cache
                    $cacheData[$cacheKey] = $menuItem;

                    $this->runtimeCache->setCache('complementObjects', $cacheData);
                }

                // Use the cached item
                $menuItems[$key] = $cacheData[$cacheKey];
            }
        }

        return $menuItems;
    }
}