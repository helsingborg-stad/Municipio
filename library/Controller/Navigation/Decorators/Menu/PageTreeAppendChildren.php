<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use Municipio\Helper\GetGlobal;
use WpService\Contracts\GetPostType;

/**
 * Append children to page tree
 */
class PageTreeAppendChildren implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private GetPostType $wpService)
    {
    }

    /**
     * Retrieves the menu with appended children to page tree.
     *
     * @return array The menu with appended children to page tree.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $menuItemsIdAsKey = [];
        foreach ($menu['items'] as $menuItem) {
            $menuItemsIdAsKey[$menuItem['id']] = $menuItem;
        }

        $newMenuItems = [];
        foreach ($menu['items'] as &$menuItem) {
            if (!empty($menuItem['isCached'])) {
                continue;
            }

            if ($menuItem['id'] == CurrentPostId::get()) {
                $children = GetPostsByParent::getPostsByParent(
                    $menuItem['id'],
                    $this->wpService->getPostType($menuItem['id'])
                );
            } else {
                $children = $this->indicateChildren($menuItem['id']);
            }

            //If null, no children
            $structuredChildren      = [];
            $hasUnstructuredChildren = false;
            if (is_array($children) && !empty($children)) {
                foreach ($children as &$child) {
                    if (isset($menuItemsIdAsKey[$child['ID']])) {
                        $structuredChildren[] = $menuItemsIdAsKey[$child['ID']];
                    } else {
                        $newMenuItems[]          = $child;
                        $hasUnstructuredChildren = true;
                    }
                }

                $menuItem['children'] = !empty($structuredChildren) ? $structuredChildren : $hasUnstructuredChildren;
            } elseif (!empty($children)) {
                $menuItem['children'] = $children;
            }
        }

        $menu['items'] = array_merge($menu['items'], $newMenuItems);

        return $menu;
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

    /**
     * Retrieves the configuration of the menu.
     *
     * @return MenuConfigInterface The configuration of the menu.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
