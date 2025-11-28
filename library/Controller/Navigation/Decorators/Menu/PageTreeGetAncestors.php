<?php

namespace Municipio\Controller\Navigation\Decorators\Menu;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestors;
use Municipio\Controller\Navigation\MenuInterface;
use WpService\Contracts\ApplyFilters;

/**
 * Page tree get ancestors
 */
class PageTreeGetAncestors implements MenuInterface
{
    /**
     * Constructor
     */
    public function __construct(private MenuInterface $inner, private ApplyFilters $wpService)
    {
    }

    /**
     * Retrieves the menu with applied menu item filters.
     *
     * @return array The menu with applied menu item filters.
     */
    public function getMenu(): array
    {
        $menu = $this->inner->getMenu();

        if (is_int($this->getConfig()->getFallbackToPageTree())) {
            $menu['items'] = [$this->getConfig()->getFallbackToPageTree()];
        } else {
            $menu['items'] = GetAncestors::getAncestors();
        }

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
