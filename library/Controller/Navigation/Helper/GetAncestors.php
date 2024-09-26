<?php

namespace Municipio\Controller\Navigation\Helper;

use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class GetAncestors
{
    private int $postId;

    public function __construct(
        private GetPageForPostTypeIds $getPageForPostTypeIdsInstance
    ) {
    }
    
    /**
     * Fetch the current page/posts parent, with support for page for posttype.
     *
     * @param   array   $postId    The current post id
     *
     * @return  array              Flat array with parents
     */
    public function getAncestors(MenuConfigInterface $menuConfig): array
    {
        $this->postId = $menuConfig->getPageId();

        $cacheSubKey = $menuConfig->getIncludeTopLevel() ? 'toplevel' : 'notoplevel';
        if (isset($menuConfig->getRuntimeCache()->getCache('ancestors')[$cacheSubKey][$this->postId])) {
            return $menuConfig->getRuntimeCache()->getCache('ancestors')[$cacheSubKey][$this->postId];
        }

        //Definitions
        $ancestorStack  = array($this->postId);
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
        ", $this->postId)
            );

            //About to end, is there a linked pfp page?
            if ($ancestorID == 0) {
                //Get posttype of post
                $currentPostType    = get_post_type($this->postId);
                $pageForPostTypeIds = array_flip($this->getPageForPostTypeIdsInstance->get($menuConfig));

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
        if ($menuConfig->getIncludeTopLevel() === true) {
            $ancestorStack = array_merge(
                [0],
                $ancestorStack
            );
        }

        //Return and cache result
        $ancestors = $menuConfig->getRuntimeCache()->getCache('ancestors');
        $ancestors[$cacheSubKey][$this->postId] = $ancestorStack;
        $menuConfig->getRuntimeCache()->setCache('ancestors', $ancestors);

        return $ancestorStack;
    }

}