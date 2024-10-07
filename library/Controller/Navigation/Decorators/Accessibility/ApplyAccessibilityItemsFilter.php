<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;
use WpService\Contracts\ApplyFilters;

class ApplyAccessibilityItemsFilter implements NewMenuInterface
{
    public function __construct(private NewMenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();

        return $this->wpService->applyFilters('Municipio/Accessibility/Items', $menuItems);
    }

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}