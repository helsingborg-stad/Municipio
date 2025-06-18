<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Controller\Navigation\Cache\NavigationWpCache;
use Municipio\Helper\GetGlobal;
use Municipio\Controller\Navigation\Helper\IsUserLoggedIn;

/**
 * Get hidden post ids
 */
class GetHiddenPostIds
{
    /**
     * Get a list of hidden post id's
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
    public static function getHiddenPostIds(string $metaKey = "hide_in_menu"): array
    {
        //Get cached result
        $cache = NavigationWpCache::getCache($metaKey);
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        $localWpdb = GetGlobal::getGlobal('wpdb');

        $postStatus = IsUserLoggedIn::isUserLoggedIn() ?
            "post_status IN('publish', 'private')" :
            "post_status = 'publish'";        

        //Get meta
        $hiddenPages = (array) $localWpdb->get_col(
            $localWpdb->prepare(
                "
                SELECT post_id
                FROM {$localWpdb->postmeta} AS pm 
                JOIN {$localWpdb->posts} AS p ON pm.post_id = p.ID
                WHERE meta_key = %s
                AND meta_value = '1'
                AND {$postStatus}
            ",
                $metaKey
            )
        );

        //Do not let the array return be empty
        if (empty($hiddenPages)) {
            //Declare result
            $hiddenPages = [PHP_INT_MAX];
        }

        //Cache
        NavigationWpCache::setCache($metaKey, $hiddenPages);

        return $hiddenPages;
    }
}
