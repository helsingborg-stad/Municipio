<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

/**
 * Page tree set menu items cache
 */
class PageTreeSetMenuItemsCache implements MenuInterface
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
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $cacheData = NavigationRuntimeCache::getCache('complementObjects');
        foreach ($menu['items'] as &$menuItem) {
            if (!empty($menuItem['isCached'])) {
                continue;
            }

            if (empty($menuItem['cacheKey'])) {
                $menuItem['cacheKey'] = md5(serialize($menuItem));
            }

            $cacheData[$menuItem['cacheKey']] = $menuItem;
            NavigationRuntimeCache::setCache('complementObjects', $cacheData);
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
