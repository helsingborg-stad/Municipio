<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Controller\Navigation\Cache\CacheManagerInterface;

class GetPageForPostTypeIds
{
    public function __construct(
        private CacheManagerInterface $cacheManager
    ) {
    }

    /**
     * Get all post id's mapped as a post type container.
     *
     * @return array
     */
    public function get(): array
    {
        //Get cached result
        $cache = $this->cacheManager->getCache('pageForPostType');
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
        $this->cacheManager->setCache('pageForPostType', $result);

        return $result;
    }
}