<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItems;

use Municipio\Controller\Navigation\Cache\CacheManagerInterface;
use Municipio\Controller\Navigation\Cache\RuntimeCache;

class ComplementPageTreeMenuItemsDecorator implements PageTreeDecoratorInterface
{
    public function __construct(
        private string $identifier,
        private array $menuItemdecorators,
        private RuntimeCache $runtimeCache
    ) {}

    /**
     * Calculate add add data to array
     *
     * @param   array    $menuItems     The post array
     *
     * @return  array    $menuItems     The post array, with appended data
     */
    public function decorate(array $menuItems): array
    {
        if (is_array($menuItems) && !empty($menuItems)) {
            foreach ($menuItems as $key => $menuItem) {
                // Generate a unique cache key for each item
                $cacheKey = md5(serialize($menuItem));

                // Check if the item is already in the cache
                $cacheData = $this->runtimeCache->getCache('complementObjects');

                if (!isset($cacheData[$cacheKey])) {
                    // Process the item and add it to the cache
                    foreach ($this->menuItemdecorators as $decorator) {
                        $menuItem = $decorator->decorate($menuItem);
                    }

                    // Store the processed item in the cache
                    $cacheData[$cacheKey] = apply_filters(
                        'Municipio/Navigation/Item',
                        $menuItem,
                        $this->identifier,
                        false
                    );

                    $this->runtimeCache->setCache('complementObjects', $cacheData);
                }

                // Use the cached item
                $menuItems[$key] = $cacheData[$cacheKey];
            }
        }

        return $menuItems;
    }
}