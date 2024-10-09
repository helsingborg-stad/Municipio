<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\WP;

class AppendPageTreeAncestorsMenuItems implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();
        if (is_front_page() || is_archive()) {
            return $menuItems;
        }

        $ancestors = GetAncestors::getAncestors();

        if (!empty($ancestors)) {
            $ancestors = array_reverse($ancestors);
            array_pop($ancestors);

            $pageForPostTypeIds = array_flip(GetPageForPostTypeIds::getPageForPostTypeIds());

            //Add items
            foreach ($ancestors as $id) {
                if (!in_array($id, $pageForPostTypeIds)) {
                    $title                     = WP::getTheTitle($id);
                    $menuItems[$id]['label']   = $title ? $title : __("Untitled page", 'municipio');
                    $menuItems[$id]['href']    = WP::getPermalink($id);
                    $menuItems[$id]['current'] = false;
                    $menuItems[$id]['icon']    = 'chevron_right';
                }
            }
        }

        return $menuItems;
    }

    public function getMenu(): array
    {
        return $this->inner->getMenu();
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}