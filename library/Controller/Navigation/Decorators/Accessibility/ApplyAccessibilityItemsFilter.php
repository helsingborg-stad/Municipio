<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

/**
 * Apply accessibility items filter
 */
class ApplyAccessibilityItemsFilter implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    /**
     * Retrieves the menu with applied accessibility items filters.
     *
     * @return array The menu with applied accessibility items filters.
     */
    public function getMenu(): array
    {
        $menu          = $this->inner->getMenu();
        $menu['items'] = $this->wpService->applyFilters('Municipio/Accessibility/Items', $menu['items']);

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
