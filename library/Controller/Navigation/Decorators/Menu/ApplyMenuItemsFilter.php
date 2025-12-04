<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

/**
 * Apply menu items filter
 */
class ApplyMenuItemsFilter implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    /**
     * Retrieves the menu with applied menu items filters.
     *
     * @return array The menu with applied menu items filters.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        $menu['items'] = $this->wpService->applyFilters('Municipio/Navigation/Items', $menu['items'], $this->getConfig()->getIdentifier());

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
