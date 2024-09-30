<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Cache\NavigationRuntimeCache;
use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;


class GetAncestors
{    
    /**
     * Fetch the current page/posts parent, with support for page for posttype.
     *
     * @param   array   $postId    The current post id
     *
     * @return  array              Flat array with parents
     */
    public static function getAncestors(MenuConfigInterface $menuConfig, $includeTopLevel = true): array
    {
        $postId = $menuConfig->getPageId();

        $cacheSubKey = $includeTopLevel ? 'toplevel' : 'notoplevel';
        if (isset(NavigationRuntimeCache::getCache('ancestors')[$cacheSubKey][$postId])) {
            return NavigationRuntimeCache::getCache('ancestors')[$cacheSubKey][$postId];
        }

        //Definitions
        $ancestorStack  = array($postId);
        $fetchAncestors = true;

        //Fetch ancestors
        while ($fetchAncestors) {
            $ancestorID = $menuConfig->getWpdb()->get_var(
                $menuConfig->getWpdb()->prepare("
            SELECT post_parent
            FROM  " . $menuConfig->getWpdb()->posts . "
            WHERE ID = %d
            AND post_status = 'publish'
            LIMIT 1
        ", $postId)
            );

            //About to end, is there a linked pfp page?
            if ($ancestorID == 0) {
                //Get posttype of post
                $currentPostType    = get_post_type($postId);
                $pageForPostTypeIds = array_flip(GetPageForPostTypeIds::getPageForPostTypeIds($menuConfig));

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
                $postId = $ancestorID;
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
        $ancestors = NavigationRuntimeCache::getCache('ancestors');
        $ancestors[$cacheSubKey][$postId] = $ancestorStack;
        NavigationRuntimeCache::setCache('ancestors', $ancestors);

        return $ancestors[$cacheSubKey][$postId];
    }

}