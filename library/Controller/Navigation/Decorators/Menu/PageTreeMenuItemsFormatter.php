<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;

class PageTreeMenuItemsFormatter implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        foreach ($menu['items'] as &$menuItem) {
            if (!empty($menuItem['isCached'])) {
                continue;
            }

            //Move post_title to label key
            $menuItem['label']       = $menuItem['post_title'];
            $menuItem['id']          = (int) $menuItem['ID'];
            $menuItem['post_parent'] = (int) $menuItem['post_parent'];

            //Unset data not needed
            unset($menuItem['post_title']);
            unset($menuItem['ID']);

            //Sort & return
            $menuItem = array_merge(
                array(
                    'id'          => null,
                    'post_parent' => null,
                    'post_type'   => null,
                    'active'      => null,
                    'ancestor'    => null,
                    'label'       => null,
                    'href'        => null,
                    'children'    => null
                ),
                $menuItem
            );
        }

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
