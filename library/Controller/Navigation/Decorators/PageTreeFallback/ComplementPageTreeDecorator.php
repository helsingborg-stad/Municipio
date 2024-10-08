<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;

class ComplementPageTreeDecorator implements MenuItemsDecoratorInterface
{
    private $masterPostType = 'page';

    public function __construct(
        private array $menuItemDecorators,
    ) {
    }

    /**
     * Decorates an array of menu items based on the provided menu configuration.
     *
     * @param array $menuItems The array of menu items to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @return array The decorated array of menu items.
     */
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        if (!empty($menuItems) || !$menuConfig->getFallbackToPageTree() || !is_numeric($menuConfig->getPageId())) {
            return $menuItems;
        }

        // Get all ancestors
        $menuItems = $menuConfig->getFallbackToPageTreeAncestors() ? GetAncestors::getAncestors() : $menuConfig->getPageId();

        // Get all parents
        $menuItems = GetPostsByParent::getPostsByParent($menuItems, [$this->masterPostType, $menuConfig->getPostType()]);

        if (is_array($menuItems) && !empty($menuItems)) {
            foreach ($menuItems as $key => $menuItem) {
                // Generate a unique cache key for each item
                $cacheKey = md5(serialize($menuItem));

                // Check if the item is already in the cache
                $cacheData = NavigationRuntimeCache::getCache('complementObjects');

                if (!isset($cacheData[$cacheKey])) {
                    // Structures the menu item as the first step

                    foreach ($this->menuItemDecorators as $decorator) {
                        $menuItem = $decorator->decorate($menuItem, $menuConfig, $this);
                    }

                    // Store the processed item in the cache
                    $cacheData[$cacheKey] = $menuItem;

                    NavigationRuntimeCache::setCache('complementObjects', $cacheData);
                }

                // Use the cached item
                $menuItems[$key] = $cacheData[$cacheKey];
            }
        }
        
        return $menuItems;
    }

    public static function factory(array $decorators): self
    {
        return new self($decorators);
    }
}
