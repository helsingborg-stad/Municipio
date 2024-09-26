<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\TransformPageTreeFallbackMenuItemDecorator;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;

class ComplementPageTreeDecorator implements MenuItemsDecoratorInterface
{
    private $masterPostType = 'page';

    public function __construct(
        private GetAncestors $getAncestorsInstance,
        private GetPostsByParent $getPostsByParentInstance,
        private TransformPageTreeFallbackMenuItemDecorator $transformPageTreeFallbackMenuItemDecoratorInstance,
        private array $menuItemDecorators
    ) {}

    /**
     * Calculate add add data to array
     *
     * @param   array    $menuItems     The post array
     *
     * @return  array    $menuItems     The post array, with appended data
     */
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        if (!empty($menuItems) || !$menuConfig->getFallbackToPageTree() || !is_numeric($menuConfig->getPageId())) {
            return $menuItems;
        }

        // Get all ancestors
        $menuItems = $this->getAncestorsInstance->getAncestors($menuConfig);

        // Get all parents
        $menuItems = $this->getPostsByParentInstance->getPostsByParent($menuConfig, $menuItems, [$this->masterPostType, get_post_type()]);

        if (is_array($menuItems) && !empty($menuItems)) {
            foreach ($menuItems as $key => $menuItem) {
                // Generate a unique cache key for each item
                $cacheKey = md5(serialize($menuItem));

                // Check if the item is already in the cache
                $cacheData = $menuConfig->getRuntimeCache()->getCache('complementObjects');

                if (!isset($cacheData[$cacheKey])) {
                    // Structures the menu item as the first step
                    $menuItem = $this->transformPageTreeFallbackMenuItemDecoratorInstance->decorate($menuItem, $menuConfig);

                    foreach ($this->menuItemDecorators as $decorator) {
                        $menuItem = $decorator->decorate($menuItem, $menuConfig);
                    }

                    // Store the processed item in the cache
                    $cacheData[$cacheKey] = $menuItem;

                    $menuConfig->getRuntimeCache()->setCache('complementObjects', $cacheData);
                }

                // Use the cached item
                $menuItems[$key] = $cacheData[$cacheKey];
            }
        }

        return $menuItems;
    }
}