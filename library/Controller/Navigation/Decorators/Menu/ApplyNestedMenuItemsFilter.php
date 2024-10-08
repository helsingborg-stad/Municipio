<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

class ApplyNestedMenuItemsFilter implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        if (empty($menuItems)) {
            return [];
        }

        return apply_filters('Municipio/Navigation/Nested', $menuItems, $this->getConfig()->getIdentifier(), $this->getConfig()->getMenuName());
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}