<?php

namespace Municipio\Controller\Navigation\Helper;

use Closure;
use Municipio\Controller\Navigation\Cache\NavigationWpCache;

class GetPageForPostTypeIds
{
    private const CACHE_KEY = 'pageForPostType:v2';

    /**
     * Get all post id's mapped as a post type container.
     *
     * @param ?array   $postTypes Optional post types for testing.
     * @param ?Closure $optionResolver Optional page-for-post-type resolver for testing.
     * @param bool     $bypassCache Optional cache bypass for testing.
     *
     * @return array
     */
    public static function getPageForPostTypeIds(
        ?array $postTypes = null,
        ?Closure $optionResolver = null,
        bool $bypassCache = false
    ): array
    {
        //Get cached result
        $cache = NavigationWpCache::getCache(self::CACHE_KEY);
        if (!$bypassCache && !is_null($cache) && is_array($cache)) {
            return $cache;
        }

        //Declare results array
        $result = array();

        $postTypes = $postTypes ?? get_post_types([
            'public' => true,
        ]);
        $optionResolver = $optionResolver ?? static fn (string $postType): mixed => get_option('page_for_' . $postType, true);

        //Check for results
        if (is_countable($postTypes)) {
            foreach ($postTypes as $postType) {
                //Fetch mapping ID
                $postId = $optionResolver($postType);
                $postId = apply_filters('Municipio/Navigation/PageForPostTypeId', $postId, $postType);

                //Validate mapping ID
                if (is_numeric($postId)) {
                    $result[$postId] = $postType;
                }
            }
        }

        //Cache
        if (!$bypassCache) {
            NavigationWpCache::setCache(self::CACHE_KEY, $result);
        }

        return $result;
    }
}
