<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

class TryGetPageTreeMenuItemsFromCache implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();
        $cacheData = NavigationRuntimeCache::getCache('complementObjects');

        foreach ($menuItems as &$menuItem) {
            $cacheKey = md5(serialize($menuItem));

            if (isset($cacheData[$cacheKey])) {
                $menuItem = $cacheData[$cacheKey];
                $menuItem['isCached'] = true;
            } else {
                $menuItem['isCached'] = false;
            }

            $menuItem['cacheKey'] = $cacheKey;
        }

        return $menuItems;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}