<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Controller\Navigation\Cache\CacheManagerInterface;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class GetPageForPostTypeIds
{
    public function __construct(
    ) {
    }

    /**
     * Get all post id's mapped as a post type container.
     *
     * @return array
     */
    public function get(MenuConfigInterface $menuConfig): array
    {
        //Get cached result
        $cache = $menuConfig->getCacheManager()->getCache('pageForPostType');
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        //Declare results array
        $result = array();

        //Only supported for hierarchical
        $postTypes = get_post_types([
            'public'       => true,
            'hierarchical' => true
        ]);

        //Check for results
        if (is_countable($postTypes)) {
            foreach ($postTypes as $postType) {
                //Fetch mapping ID
                $postId = get_option('page_for_' . $postType, true);

                //Validate mapping ID
                if (is_numeric($postId)) {
                    $result[$postId] = $postType;
                }
            }
        }

        //Cache
        $menuConfig->getCacheManager()->setCache('pageForPostType', $result);

        return $result;
    }
}