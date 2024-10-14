<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetQueriedObject;

class ApplyBreadcrumbItemsFilter implements MenuInterface
{
    public function __construct(private MenuInterface $inner, private ApplyFilters&GetQueriedObject $wpService)
    {
    }

    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $menu['items'] = $this->wpService->applyFilters('Municipio/Breadcrumbs/Items', $menu['items'], $this->wpService->getQueriedObject(), 'municipio');

        return $menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
