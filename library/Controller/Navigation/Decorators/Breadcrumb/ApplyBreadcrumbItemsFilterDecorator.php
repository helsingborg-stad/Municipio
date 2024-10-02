<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use WpService\Contracts\ApplyFilters;

class ApplyBreadcrumbItemsFilterDecorator implements MenuItemsDecoratorInterface
{
    public function __construct(private ApplyFilters $wpService)
    {
    }

    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        return $this->wpService->applyFilters('Municipio/Breadcrumbs/Items', $menuItems, get_queried_object(), $menuConfig->getContext());
    }
}