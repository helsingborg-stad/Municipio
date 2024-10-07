<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\NewMenuInterface;
use Municipio\Helper\GetGlobal;

class PageTreeAppendChildren implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        if (empty($menuItems)) {
            return $menuItems;
        }

        $menuItemsIdAsKey = [];
        foreach ($menuItems as $menuItem) {
            $menuItemsIdAsKey[$menuItem['id']] = $menuItem;
        }

        foreach ($menuItems as &$menuItem) {
            if (!empty($menuItem['isCached'])) {
                continue;
            }

            if ($menuItem['id'] == $this->getConfig()->getPageId()) {
                $children = GetPostsByParent::getPostsByParent(
                    $menuItem['id'],
                    get_post_type($menuItem['id'])
                );
            } else {
                $children = $this->indicateChildren($menuItem['id']);
            }
    
            //If null, no children
            if (is_array($children) && !empty($children)) {
                foreach ($children as &$child) {
                    if (isset($menuItemsIdAsKey[$child['ID']])) {
                        $child = $menuItemsIdAsKey[$child['ID']];
                    }
                }
            } 

            $menuItem['children'] = $children;
        }

        return $menuItems;
    }

        /**
     * Indicates whether a post has children or not.
     *
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @param int $postId The ID of the post.
     * @return bool Returns true if the post has children, false otherwise.
     */
    private function indicateChildren($postId): bool
    {
        //Define to omit error
        $postTypeHasPosts = null;

        $localWpdb = GetGlobal::getGlobal('wpdb');

        $currentPostTypeChildren = $localWpdb->get_var(
            $localWpdb->prepare("
        SELECT ID
        FROM " . $localWpdb->posts . "
        WHERE post_parent = %d
        AND post_status = 'publish'
        AND ID NOT IN(" . implode(", ", GetHiddenPostIds::getHiddenPostIds()) . ")
        LIMIT 1
      ", $postId)
        );

        //Check if posttype has content
        $pageForPostTypeIds = GetPageForPostTypeIds::getPageForPostTypeIds();
        if (array_key_exists($postId, $pageForPostTypeIds)) {
            $postTypeHasPosts = $localWpdb->get_var(
                $localWpdb->prepare("
                    SELECT ID
                    FROM " . $localWpdb->posts . "
                    WHERE post_parent = 0
                    AND post_status = 'publish'
                    AND post_type = %s
                    AND ID NOT IN(" . implode(", ", GetHiddenPostIds::getHiddenPostIds()) . ")
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

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}