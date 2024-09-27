<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\ComplementPageTreeDecorator;

class AppendChildrenDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    /**
     * Decorates a menu item with its children.
     *
     * This method decorates a menu item by adding its children to the 'children' key of the menu item array.
     * If the menu item's ID matches the page ID specified in the menu configuration, it retrieves the children
     * using the GetPostsByParent::getPostsByParent() method. Otherwise, it indicates the children using the
     * $this->indicateChildren() method.
     *
     * @param array $menuItem The menu item to decorate.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param ComplementPageTreeDecorator $parentInstance The parent instance of the ComplementPageTreeDecorator.
     * @return array The decorated menu item.
     */
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig, ComplementPageTreeDecorator $parentInstance): array
    {
        if ($menuItem['id'] == $menuConfig->getPageId()) {
            $children = GetPostsByParent::getPostsByParent(
                $menuConfig,
                $menuItem['id'],
                get_post_type($menuItem['id'])
            );
        } else {
            $children = $this->indicateChildren($menuConfig, $menuItem['id']);
        }

        //If null, no children
        if (is_array($children) && !empty($children) && $parentInstance) {
            $menuItem['children'] = $parentInstance->decorate($children, $menuConfig);
        } else {
            $menuItem['children'] = (bool) $children;
        }

        //Return result
        return $menuItem;
    }

    /**
     * Indicates whether a post has children or not.
     *
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @param int $postId The ID of the post.
     * @return bool Returns true if the post has children, false otherwise.
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
        AND ID NOT IN(" . implode(", ", GetHiddenPostIds::getHiddenPostIds($menuConfig)) . ")
        LIMIT 1
      ", $postId)
        );

        //Check if posttype has content
        $pageForPostTypeIds = GetPageForPostTypeIds::getPageForPostTypeIds($menuConfig);
        if (array_key_exists($postId, $pageForPostTypeIds)) {
            $postTypeHasPosts = $menuConfig->getWpdb()->get_var(
                $menuConfig->getWpdb()->prepare("
                    SELECT ID
                    FROM " . $menuConfig->getWpdb()->posts . "
                    WHERE post_parent = 0
                    AND post_status = 'publish'
                    AND post_type = %s
                    AND ID NOT IN(" . implode(", ", GetHiddenPostIds::getHiddenPostIds($menuConfig)) . ")
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