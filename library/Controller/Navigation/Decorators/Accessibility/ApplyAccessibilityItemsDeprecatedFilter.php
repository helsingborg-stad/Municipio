<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFiltersDeprecated;

/**
 * Apply accessibility items deprecated filter
 */
class ApplyAccessibilityItemsDeprecatedFilter implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private ApplyFiltersDeprecated $wpService)
    {
    }

    /**
     * Retrieves the menu with applied accessibility items filters.
     *
     * @return array The menu with applied accessibility items filters.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $menu['items'] = $this->wpService->applyFiltersDeprecated('accessibility_items', [$menu['items']], '3.0.0', 'Municipio/Accessibility/Items');

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
