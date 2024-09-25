<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

class TransformMenuItemDecorator implements DefaultMenuItemDecoratorInterface
{
    public function __construct(
        private int $pageId
    ) {
    }

    public function decorate(array|object $menuItem, array $ancestors): array
    {
        return [
            'id'          => $menuItem->ID,
            'post_parent' => $menuItem->menu_item_parent,
            'post_type'   => $menuItem->object,
            'page_id'     => $menuItem->object_id,
            'active'      => ($menuItem->object_id == $this->pageId) || \Municipio\Helper\IsCurrentUrl::isCurrentOrAncestorUrl($menuItem->url),
            'label'       => $menuItem->title,
            'href'        => $menuItem->url,
            'children'    => false,
            'top_level'   => $menuItem->menu_item_parent == 0,
            'xfn'         => $menuItem->xfn ?? false
        ];
    }
}