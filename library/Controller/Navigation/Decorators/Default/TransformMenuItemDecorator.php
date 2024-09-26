<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class TransformMenuItemDecorator implements DefaultMenuItemDecoratorInterface
{
    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig, array $ancestors): array
    {
        return [
            'id'          => $menuItem->ID,
            'post_parent' => $menuItem->menu_item_parent,
            'post_type'   => $menuItem->object,
            'page_id'     => $menuItem->object_id,
            'active'      => ($menuItem->object_id == $menuConfig->getPageId()) || \Municipio\Helper\IsCurrentUrl::isCurrentOrAncestorUrl($menuItem->url),
            'label'       => $menuItem->title,
            'href'        => $menuItem->url,
            'children'    => false,
            'top_level'   => $menuItem->menu_item_parent == 0,
            'xfn'         => $menuItem->xfn ?? false
        ];
    }
}