<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Controller\Navigation\Cache\CacheManagerInterface;

class GetHiddenPostIds
{
    public function __construct(
        private $db,
        private CacheManagerInterface $cacheManager
    ) {
    }
    
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
    public function get(string $metaKey = "hide_in_menu"): array
    {
        //Get cached result
        $cache = $this->cacheManager->getCache($metaKey);
        if (!is_null($cache) && is_array($cache)) {
            return $cache;
        }

        //Get meta
        $hiddenPages = (array) $this->db->get_col(
            $this->db->prepare(
                "
                SELECT post_id
                FROM " . $this->db->postmeta . " AS pm 
                JOIN " . $this->db->posts . " AS p ON pm.post_id = p.ID
                WHERE meta_key = %s
                AND meta_value = '1'
                AND post_status = 'publish'
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
        $this->cacheManager->setCache($metaKey, $hiddenPages);

        return $hiddenPages;
    }
}