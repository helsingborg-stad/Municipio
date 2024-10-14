<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\GetGlobal;

class PageTreeAppendMenuItemsCustomTitle implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            if (!empty($menuItem['isCached'])) {
                continue;
            }

            $customTitles = $this->getMenuTitle();

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
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @param string $metaKey The meta key to filter the menu titles.
     * @return array The array of menu titles with their corresponding page IDs.
     */
    private function getMenuTitle(string $metaKey = "custom_menu_title"): array
    {
        //Get cached result
        $cache = NavigationRuntimeCache::getCache($metaKey);
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        $localWpdb = GetGlobal::getGlobal('wpdb');

        //Get meta
        $result = (array) $localWpdb->get_results(
            $localWpdb->prepare(
                "
                SELECT post_id, meta_value
                FROM " . $localWpdb->postmeta . " as pm
                JOIN " . $localWpdb->posts . " AS p ON pm.post_id = p.ID
                WHERE meta_key = %s
                AND meta_value != ''
                AND post_status = 'publish'
            ",
                $metaKey
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
        NavigationRuntimeCache::setCache($metaKey, $pageTitles);

        return $pageTitles;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
