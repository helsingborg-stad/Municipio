<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

class ApplyAccessibilityItemsDeprecatedFilter implements MenuInterface
{
    public function __construct(private MenuInterface $inner)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();
        
        return apply_filters_deprecated('accessibility_items', [$menuItems], '3.0.0', 'Municipio/Accessibility/Items');
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