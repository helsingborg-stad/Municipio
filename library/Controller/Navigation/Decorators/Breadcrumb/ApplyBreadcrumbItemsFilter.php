<?php

namespace Municipio\Controller\Navigation\Decorators\Breadcrumb;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetQueriedObject;

/**
 * Apply breadcrumb items filter
 */
class ApplyBreadcrumbItemsFilter implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private ApplyFilters&GetQueriedObject $wpService)
    {
    }

    /**
     * Retrieves the menu with applied breadcrumb items filters.
     *
     * @return array The menu with applied breadcrumb items filters.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $menu['items'] = $this->wpService->applyFilters('Municipio/Breadcrumbs/Items', $menu['items'], $this->wpService->getQueriedObject(), 'municipio');

        return $menu;
    }

    /**
     * Retrieves the menu configuration.
     *
     * @return MenuConfigInterface The menu configuration.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->inner->getConfig();
    }
}
