<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

/**
 * Apply nested menu items filter
 */
class ApplyNestedMenuItemsFilter implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    /**
     * Retrieves the menu with applied nested menu items filters.
     *
     * @return array The menu with applied nested menu items filters.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (empty($menu['items'])) {
            return $menu;
        }

        $menu['items'] = $this->wpService->applyFilters('Municipio/Navigation/Nested', $menu['items'], $this->getConfig()->getIdentifier(), $this->getConfig()->getMenuName());

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
