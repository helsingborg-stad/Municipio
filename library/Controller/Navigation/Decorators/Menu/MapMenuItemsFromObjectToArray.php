<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;

class MapMenuItemsFromObjectToArray implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        if (empty($menuItems)) {
            return $menuItems;
        }

        foreach ($menuItems as &$menuItem) {
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
                'xfn'         => $menuItem->xfn ?? false
            ];
        }


        return $menuItems;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}