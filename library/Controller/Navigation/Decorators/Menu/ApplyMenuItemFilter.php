<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

class ApplyMenuItemFilter implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private ApplyFilters $wpService)
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
            
            $menuItem = $this->wpService->applyFilters('Municipio/Navigation/Item', $menuItem, $this->getConfig()->getIdentifier(), true);
        }

        return $menuItems;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}