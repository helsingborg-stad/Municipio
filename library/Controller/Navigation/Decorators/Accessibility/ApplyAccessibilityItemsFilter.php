<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

class ApplyAccessibilityItemsFilter implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    public function getMenu(): array
    {
        $menu          = $this->inner->getMenu();
        $menu['items'] = $this->wpService->applyFilters('Municipio/Accessibility/Items', $menu['items']);

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
