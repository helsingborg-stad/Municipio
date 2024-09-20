<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Cache\CacheManagerInterface;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;

class GetAncestors
{

    public function __construct(
        private $postId,
        private $db,
        private CacheManagerInterface $runtimeCacheInstance,
        private GetPageForPostTypeIds $getPageForPostTypeIdsInstance
    ) {}
    
    /**
     * Fetch the current page/posts parent, with support for page for posttype.
     *
     * @param   array   $postId    The current post id
     *
     * @return  array              Flat array with parents
     */
    public function getAncestors($includeTopLevel = true): array
    {

        $cacheSubKey = $includeTopLevel ? 'toplevel' : 'notoplevel';
        if (isset($this->runtimeCacheInstance->getCache('ancestors')[$cacheSubKey][$this->postId])) {
            return $this->runtimeCacheInstance->getCache('ancestors')[$cacheSubKey][$this->postId];
        }

        //Definitions
        $ancestorStack  = array($this->postId);
        $fetchAncestors = true;

        //Fetch ancestors
        while ($fetchAncestors) {
            $ancestorID = $this->db->get_var(
                $this->db->prepare("
            SELECT post_parent
            FROM  " . $this->db->posts . "
            WHERE ID = %d
            AND post_status = 'publish'
            LIMIT 1
        ", $this->postId)
            );

            //About to end, is there a linked pfp page?
            if ($ancestorID == 0) {
                //Get posttype of post
                $currentPostType    = get_post_type($this->postId);
                $pageForPostTypeIds = array_flip($this->getPageForPostTypeIdsInstance->get());

                //Look for replacement
                if ($currentPostType && array_key_exists($currentPostType, $pageForPostTypeIds)) {
                    $ancestorID = $pageForPostTypeIds[$currentPostType];
                }

                //No replacement found
                if ($ancestorID == 0) {
                    $fetchAncestors = false;
                }
            }

            if ($fetchAncestors !== false) {
                //Add to stack (with duplicate prevention)
                if (!in_array($ancestorID, $ancestorStack)) {
                    $ancestorStack[] = (int) $ancestorID;
                }

                //Prepare for next iteration
                $this->postId = $ancestorID;
            }
        }

        //Include zero level
        if ($includeTopLevel === true) {
            $ancestorStack = array_merge(
                [0],
                $ancestorStack
            );
        }

        //Return and cache result
        $ancestors = $this->runtimeCacheInstance->getCache('ancestors');
        $ancestors[$cacheSubKey][$this->postId] = $ancestorStack;
        $this->runtimeCacheInstance->setCache('ancestors', $ancestors);

        return $ancestorStack;
    }

}