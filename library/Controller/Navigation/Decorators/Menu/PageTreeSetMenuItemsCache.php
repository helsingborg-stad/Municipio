<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

class PageTreeSetMenuItemsCache implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        if (empty($menuItems)) {
            return $menuItems;
        }

        $cacheData = NavigationRuntimeCache::getCache('complementObjects');
        foreach ($menuItems as &$menuItem) {
            if (!empty($menuItem['isCached'])) {
                continue;
            }

            if (empty($menuItem['cacheKey'])) {
                $menuItem['cacheKey'] = md5(serialize($menuItem));
            }

            $cacheData[$menuItem['cacheKey']] = $menuItem;
            NavigationRuntimeCache::setCache('complementObjects', $cacheData);
        }

        return $menuItems;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}