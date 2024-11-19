<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;

/**
 * Map menu items from object to array
 */
class MapMenuItemsFromObjectToArray implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner)
    {
    }

    /* Retrieves the menu with mapped menu items from object to array.
     *
     * @return array The menu with mapped menu items from object to array.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $pageForPostTypes = GetPageForPostTypeIds::getPageForPostTypeIds();

        foreach ($menu['items'] as &$menuItem) {
            $menuItem = [
                'id'          => $menuItem->ID,
                'post_parent' => $menuItem->menu_item_parent,
                'post_type'   => $menuItem->object,
                'page_id'     => $menuItem->object_id,
                'active'      => ($menuItem->object_id == CurrentPostId::get()) || \Municipio\Helper\IsCurrentUrl::isCurrentOrAncestorUrl($menuItem->url),
                'label'       => $menuItem->title,
                'href'        => $menuItem->url,
                'children'    => false,
                'top_level'   => $menuItem->menu_item_parent == 0,
                'xfn'         => $menuItem->xfn ?? false,
                'classList'   => [
                    's-post-type-' .
                    (isset($pageForPostTypes[$menuItem->object_id]) ?
                    $pageForPostTypes[$menuItem->object_id] :
                    $menuItem->object)
                ],
                'description' => $menuItem->description,
            ];
        }

        return $menu;
    }

    /**
     * Retrieves the menu configuration.
     *
     * @return MenuConfigInterface The menu configuration.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
