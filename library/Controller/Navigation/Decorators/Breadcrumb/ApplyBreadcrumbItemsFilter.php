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

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();
        // TODO: Check why 'muncipio' is passed in the filter. Used to be context in navigations helper.
        $menu['items'] = $this->wpService->applyFilters('Municipio/Breadcrumbs/Items', $menu['items'] , get_queried_object(), 'municipio');

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}