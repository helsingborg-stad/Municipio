<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;

class PageTreeSetMenuItemsCache implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner)
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

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}