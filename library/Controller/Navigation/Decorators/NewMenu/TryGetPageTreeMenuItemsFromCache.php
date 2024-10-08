<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;

class TryGetPageTreeMenuItemsFromCache implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner)
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

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}