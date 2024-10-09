<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\GetPermalink;

class PageTreeAppendMenuItemsHref implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private GetPermalink $wpService)
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

    public function getMenu(): array
    {
        return $this->inner->getMenu();
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}