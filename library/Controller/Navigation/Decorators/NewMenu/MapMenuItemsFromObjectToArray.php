<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;
use Municipio\Helper\CurrentPostId;

class MapMenuItemsFromObjectToArray implements NewMenuInterface
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

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}