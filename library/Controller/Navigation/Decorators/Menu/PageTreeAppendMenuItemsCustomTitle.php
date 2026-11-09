<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\IsUserLoggedIn;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\GetGlobal;

/**
 * Page tree append menu items custom title
 */
class PageTreeAppendMenuItemsCustomTitle implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner)
    {
    }

    /**
     * Retrieves the menu with appended custom titles.
     *
     * @return array The menu with appended custom titles.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $postIds = array_column(array_filter($menu['items'], static fn ($item) => empty($item['isCached'])), 'id');
        $customTitles = $this->getMenuTitle($postIds);

        foreach ($menu['items'] as &$menuItem) {
            if (!empty($menuItem['isCached'])) {
                continue;
            }

            //Get custom title
            if (isset($customTitles[$menuItem['id']])) {
                $menuItem['label'] = $customTitles[$menuItem['id']];
            }

            //Replace empty titles
            if ($menuItem['label'] == "") {
                $menuItem['label'] = __("Untitled page", 'municipio');
            }
        }

        return $menu;
    }

    /**
     * Retrieves the menu titles from the database based on the provided menu configuration and meta key.
     *
     * @param array $postIds The IDs of menu items that need titles.
     * @param string $metaKey The meta key to filter the menu titles.
     * @return array The array of menu titles with their corresponding page IDs.
     */
    private function getMenuTitle(array $postIds, string $metaKey = "custom_menu_title"): array
    {
        if (empty($postIds)) {
            return [];
        }
        $postIds = array_map('intval', $postIds);
        sort($postIds);
        // Separate scoped results so another menu cannot reuse an incomplete title map.
        $cacheKey = $metaKey . ':' . implode(',', $postIds);
        //Get cached result
        $cache = NavigationRuntimeCache::getCache($cacheKey);
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        $localWpdb = GetGlobal::getGlobal('wpdb');
        $placeholders = implode(', ', array_fill(0, count($postIds), '%d'));

        $postStatus = IsUserLoggedIn::isUserLoggedIn() ?
            "post_status IN('publish', 'private')" :
            "post_status = 'publish'";

        //Get meta
        $result = (array) $localWpdb->get_results(
            $localWpdb->prepare(
                "
                SELECT post_id, meta_value
                FROM {$localWpdb->postmeta} AS pm
                JOIN {$localWpdb->posts} AS p ON pm.post_id = p.ID
                WHERE meta_key = %s
                AND post_id IN({$placeholders})
                AND meta_value != ''
                AND {$postStatus}
            ",
                $metaKey,
                ...$postIds
            )
        );

        //Declare result
        $pageTitles = [];

        //Add visible page ids
        if (is_array($result) && !empty($result)) {
            foreach ($result as $result) {
                if (empty($result->meta_value)) {
                    continue;
                }
                $pageTitles[$result->post_id] = $result->meta_value;
            }
        }

        //Cache the result
        NavigationRuntimeCache::setCache($cacheKey, $pageTitles);

        return $pageTitles;
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
