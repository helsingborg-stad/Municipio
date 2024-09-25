<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem\PageTreeFallback;

use Municipio\Controller\Navigation\Cache\CacheManager;

class CustomTitleDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    public function __construct(
        private $db,
        private CacheManager $cacheManager
    ) {
    }
    
    /**
     * Replace native title with custom menu name
     *
     * @param array $menuItem
     *
     * @return object
     */
    public function decorate(array $menuItem, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        $customTitles = $this->getMenuTitle();

        //Get custom title
        if (isset($customTitles[$menuItem['id']])) {
            $menuItem['label'] = $customTitles[$menuItem['id']];
        }

        //Replace empty titles
        if ($menuItem['label'] == "") {
            $menuItem['label'] = __("Untitled page", 'municipio');
        }

        return $menuItem;
    }

    /**
     * Get a list of custom page titles
     *
     * Optimzing: It may be faster on smaller databases
     * to not use a join. This will however slow down larger sites.
     *
     * This is a calculated risk that should be caught
     * by the object cache. Tests have been made to enshure
     * good performance.
     *
     * @param string $metaKey The meta key to get data from
     *
     * @return array
     */
    private function getMenuTitle(string $metaKey = "custom_menu_title"): array
    {
        //Get cached result
        $cache = $this->cacheManager->getCache($metaKey);
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        //Get meta
        $result = (array) $this->db->get_results(
            $this->db->prepare(
                "
                SELECT post_id, meta_value
                FROM " . $this->db->postmeta . " as pm
                JOIN " . $this->db->posts . " AS p ON pm.post_id = p.ID
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

        //Cache
        $this->cacheManager->setCache($metaKey, $pageTitles);

        return $pageTitles;
    }
}