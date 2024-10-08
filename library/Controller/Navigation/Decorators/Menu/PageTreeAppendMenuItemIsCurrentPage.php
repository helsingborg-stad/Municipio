<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Helper\CurrentPostId;

class PageTreeAppendMenuItemIsCurrentPage implements MenuInterface
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

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}