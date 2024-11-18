<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\Helper\GetPostsByParent;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;
use WpService\Contracts\GetPostType;

/**
 * Standard menu with page tree subitems and ancestors appended.
 */
class StandardMenuWithPageTreeSubitemsAppendAncestors implements MenuInterface
{
    private string $masterPostType = 'page';

    /**
     * Constructor.
     *
     * @param MenuInterface $inner The inner menu.
     * @param GetPostType $wpService The WordPress service.
     */
    public function __construct(private MenuInterface $inner, private GetPostType $wpService)
    {
    }

    /**
     * Retrieves the menu with appended ancestor items.
     *
     * This method retrieves the menu using the inner decorator and appends ancestor items to it.
     * Ancestor items are determined by calling the `getAncestorIds` method on the menu items.
     * If the menu is empty or there are no ancestor items, the original menu is returned.
     * Otherwise, the ancestor items are fetched using the `getPostsByParent` method and merged with the original menu items.
     *
     * @return array The menu with appended ancestor items.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $ancestorIds = $this->getAncestorIds($menu['items']);

        if (empty($ancestorIds)) {
            return $menu;
        }

        $pageForPostTypes               = GetPageForPostTypeIds::getPageForPostTypeIds();
        [$ancestorIds, $postTypesArray] = $this->getPostTypesArray($ancestorIds, $pageForPostTypes, $menu['items']);

        $ancestorPosts = GetPostsByParent::getPostsByParent($ancestorIds, $postTypesArray);

        $menu['items'] = array_merge($menu['items'], $ancestorPosts);

        return $menu;
    }

    /**
     * Retrieves an array of post types based on the given ancestor IDs, page for post types, and menu items.
     *
     * @param array $ancestorIds The array of ancestor IDs.
     * @param array $pageForPostTypes The array of page for post types.
     * @param array $menuItems The array of menu items.
     * @return array The array containing the updated ancestor IDs and the post types array.
     */
    private function getPostTypesArray(array $ancestorIds, array $pageForPostTypes, array $menuItems): array
    {
        $ancestorPageForPostType = array_intersect_key($pageForPostTypes, $ancestorIds);
        $postTypesArray          = [];

        foreach ($menuItems as $menuItem) {
            if (isset($ancestorPageForPostType[$menuItem['id']]) && empty($menuItem['children'])) {
                $postTypesArray[] = $ancestorPageForPostType[$menuItem['id']];
                $ancestorIds[0]   = 0;
                unset($ancestorIds[$menuItem['id']]);
            }
        }

        return [
            $ancestorIds,
            !empty($postTypesArray) ?
            $postTypesArray :
            [$this->wpService->getPostType(), $this->masterPostType]
        ];
    }

    /**
     * Retrieves the ancestor IDs for the given menu items.
     *
     * @param array $menuItems The menu items to retrieve ancestor IDs for.
     * @return array The ancestor IDs.
     */
    private function getAncestorIds(array $menuItems): array
    {
        $ancestors = GetAncestors::getAncestors();
        $ancestors = array_combine($ancestors, $ancestors);
        unset($ancestors[0]);

        foreach ($menuItems as $menuItem) {
            if ($menuItem['children']) {
                unset($ancestors[$menuItem['id']]);
            }
        }

        return $ancestors;
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
