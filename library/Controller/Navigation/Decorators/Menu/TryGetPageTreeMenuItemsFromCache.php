<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Try get page tree menu items from cache
 */
class TryGetPageTreeMenuItemsFromCache implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner)
    {
    }

    /**
     * Retrieves the menu with appended menu items.
     *
     * @return array The menu with appended menu items.
     */
    public function getMenu(): array
    {
        $menu      = $this->inner->getMenu();
        $cacheData = NavigationRuntimeCache::getCache('complementObjects');

        foreach ($menu['items'] as &$menuItem) {
            $cacheKey = md5(serialize($menuItem));

            if (isset($cacheData[$cacheKey])) {
                $menuItem             = $cacheData[$cacheKey];
                $menuItem['isCached'] = true;
            } else {
                $menuItem['isCached'] = false;
            }

            $menuItem['cacheKey'] = $cacheKey;
        }

        return $menu;
    }

    /**
     * Retrieves the menu configuration.
     *
     * @return MenuConfigInterface The menu configuration.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
