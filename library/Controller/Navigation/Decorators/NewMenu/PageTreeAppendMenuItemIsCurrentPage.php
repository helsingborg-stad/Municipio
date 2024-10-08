<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;
use Municipio\Helper\CurrentPostId;

class PageTreeAppendMenuItemIsCurrentPage implements NewMenuInterface
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

            if ($menuItem['id'] == CurrentPostId::get()) {
                $menuItem['active'] = true;
            } elseif (\Municipio\Helper\IsCurrentUrl::isCurrentUrl($menuItem['href'])) {
                $menuItem['active'] = true;
            } else {
                $menuItem['active'] = false;
            }
        }

        return $menuItems;
    }

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}