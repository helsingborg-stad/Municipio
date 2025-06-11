<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetHiddenPostIds;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\Helper\IsUserLoggedIn;
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

        $menuItemsIdAsKey = $this->getIdStructuredMenuItems($menu['items']);

        $newMenuItems = [];
        foreach ($menu['items'] as &$menuItem) {
            if (!empty($menuItem['isCached']) || !empty($menuItem['children'])) {
                continue;
            }

            $children = $this->getChildrenForMenuItem($menuItem);

            $menuItem['children'] = is_array($children) ?
            $this->processChildren($children, $menuItemsIdAsKey, $newMenuItems) :
            $children;
        }

        $menu['items'] = array_merge($menu['items'], $newMenuItems);

        return $menu;
    }

    /**
     * Returns an array of menu items with their IDs as keys.
     *
     * @param array $menuItems The array of menu items.
     * @return array The array of menu items with IDs as keys.
     */
    private function getIdStructuredMenuItems(array $menuItems): array
    {
        $menuItemsIdAsKey = [];
        foreach ($menuItems as $menuItem) {
            $menuItemsIdAsKey[$menuItem['id']] = $menuItem;
        }

        return $menuItemsIdAsKey;
    }

    /**
     * Process the children of a menu item.
     *
     * This method takes an array of children, an array of menu items with their IDs as keys,
     * and a reference to an array of new menu items. It processes the children by checking if
     * each child's ID exists in the menu items array. If it does, the child is added to the
     * structured children array. If not, the child is added to the new menu items array and
     * the flag for unstructured children is set to true.
     *
     * @param array $children The array of children to process.
     * @param array $menuItemsIdAsKey The array of menu items with their IDs as keys.
     * @param array $newMenuItems A reference to the array of new menu items.
     * @return array|bool The structured children array if it is not empty, otherwise the flag
     *                   indicating the presence of unstructured children.
     */
    private function processChildren(array $children, array $menuItemsIdAsKey, &$newMenuItems): array|bool
    {
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
        }

        return !empty($structuredChildren) ? $structuredChildren : $hasUnstructuredChildren;
    }

    /**
     * Retrieves the children for a given menu item.
     *
     * @param array $menuItem The menu item data.
     * @return array|bool The children of the menu item, or false if there are no children.
     */
    private function getChildrenForMenuItem(array $menuItem): array|bool
    {
        if ($menuItem['id'] == CurrentPostId::get()) {
            return GetPostsByParent::getPostsByParent(
                $menuItem['id'],
                $this->wpService->getPostType($menuItem['id'])
            );
        }

        return $this->indicateChildren($menuItem['id']);
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

        $postStatus = IsUserLoggedIn::isUserLoggedIn() ? 
            "post_status IN('publish', 'private')" : 
            "post_status = 'publish'";

        $currentPostTypeChildren = $localWpdb->get_var(
            $localWpdb->prepare("
        SELECT ID
        FROM " . $localWpdb->posts . "
        WHERE post_parent = %d
        AND post_status = " . $postStatus . "
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
                    AND post_status = " . $postStatus . "
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
