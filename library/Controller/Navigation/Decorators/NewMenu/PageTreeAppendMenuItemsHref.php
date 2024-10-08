<?php

namespace Municipio\Controller\Navigation\Decorators\NewMenu;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;
use WpService\Contracts\GetPermalink;

class PageTreeAppendMenuItemsHref implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner, private GetPermalink $wpService)
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

            $menuItem['href'] = $this->wpService->getPermalink($menuItem['id'], false);
        }

        return $menuItems;
    }

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}