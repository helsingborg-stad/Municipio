<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFiltersDeprecated;

class ApplyAccessibilityItemsDeprecatedFilter implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private ApplyFiltersDeprecated $wpService)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $menu['items'] = $this->wpService->applyFiltersDeprecated('accessibility_items', [$menu['items']], '3.0.0', 'Municipio/Accessibility/Items');

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
