<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;

class AppendChildrenDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    public function __construct(
        private GetPostsByParent $getPostsByParentInstance,
        private GetHiddenPostIds $getHiddenPostIdsInstance,
        private GetPageForPostTypeIds $getPageForPostTypeIdsInstance,
    ) {
    }

     /**
     * Check if a post has children. If this is the current post,
     * fetch the actual children array.
     *
     * @param   array   $postId    The post id
     *
     * @return  array              Flat array with parents
     */
    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig): array
    {
        if ($menuItem['id'] == $menuConfig->getPageId()) {
            $children = $this->getPostsByParentInstance->getPostsByParent(
                $menuConfig,
                $menuItem['id'],
                get_post_type($menuItem['id'])
            );
        } else {
            $children = $this->indicateChildren($menuConfig, $menuItem['id']);
        }

        //If null, no children
        if (is_array($children) && !empty($children)) {
            $menuItem['children'] = $menuConfig->getComplementPageTreeDecoratorInstance()->decorate($children, $menuConfig);
        } else {
            $menuItem['children'] = (bool) $children;
        }

        //Return result
        return $menuItem;
    }

    /**
     * Indicate if post has children
     *
     * @param   integer   $postId     The post id
     *
     * @return  boolean               Tells wheter the post has children or not
     */
    private function indicateChildren(MenuConfigInterface $menuConfig, $postId): bool
    {
        //Define to omit error
        $postTypeHasPosts = null;

        $currentPostTypeChildren = $menuConfig->getWpdb()->get_var(
            $menuConfig->getWpdb()->prepare("
        SELECT ID
        FROM " . $menuConfig->getWpdb()->posts . "
        WHERE post_parent = %d
        AND post_status = 'publish'
        AND ID NOT IN(" . implode(", ", $this->getHiddenPostIdsInstance->get($menuConfig)) . ")
        LIMIT 1
      ", $postId)
        );

        //Check if posttype has content
        $pageForPostTypeIds = $this->getPageForPostTypeIdsInstance->get($menuConfig);
        if (array_key_exists($postId, $pageForPostTypeIds)) {
            $postTypeHasPosts = $menuConfig->getWpdb()->get_var(
                $menuConfig->getWpdb()->prepare("
                    SELECT ID
                    FROM " . $menuConfig->getWpdb()->posts . "
                    WHERE post_parent = 0
                    AND post_status = 'publish'
                    AND post_type = %s
                    AND ID NOT IN(" . implode(", ", $this->getHiddenPostIdsInstance->get($menuConfig)) . ")
                    LIMIT 1
                ", $pageForPostTypeIds[$postId])
            );
        }

        //Return indication boolean
        if (!is_null($currentPostTypeChildren)) {
            return true;
        } elseif (!is_null($postTypeHasPosts)) {
            return true;
        } else {
            return false;
        }
    }
}