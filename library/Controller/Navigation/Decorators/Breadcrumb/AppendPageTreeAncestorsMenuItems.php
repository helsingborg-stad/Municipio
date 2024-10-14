<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\Helper\GetPageForPostTypeIds;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\WP;
use WpService\Contracts\IsArchive;
use WpService\Contracts\IsFrontPage;

class AppendPageTreeAncestorsMenuItems implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private IsFrontPage&IsArchive $wpService)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if ($this->wpService->isFrontPage() || $this->wpService->isArchive()) {
            return $menu;
        }

        $ancestors = GetAncestors::getAncestors();

        if (!empty($ancestors)) {
            $ancestors = array_reverse($ancestors);
            array_pop($ancestors);

            $pageForPostTypeIds = array_flip(GetPageForPostTypeIds::getPageForPostTypeIds());

            //Add items
            foreach ($ancestors as $id) {
                if (!in_array($id, $pageForPostTypeIds)) {
                    $title                         = WP::getTheTitle($id);
                    $menu['items'][$id]['label']   = $title ? $title : __("Untitled page", 'municipio');
                    $menu['items'][$id]['href']    = WP::getPermalink($id);
                    $menu['items'][$id]['current'] = false;
                    $menu['items'][$id]['icon']    = 'chevron_right';
                }
            }
        }

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}