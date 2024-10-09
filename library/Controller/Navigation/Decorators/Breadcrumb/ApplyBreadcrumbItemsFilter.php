<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

class ApplyBreadcrumbItemsFilter implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    public function getMenuItems(): array
    {
        $menuItems = $this->inner->getMenuItems();
        // TODO: Check why 'muncipio' is passed in the filter. Used to be context in navigations helper.
        return $this->wpService->applyFilters('Municipio/Breadcrumbs/Items', $menuItems , get_queried_object(), 'municipio');
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