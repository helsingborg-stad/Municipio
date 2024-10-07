<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;

class PageTreeMenuItemsFormatter implements NewMenuInterface
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

        return $menuItems;
    }

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}