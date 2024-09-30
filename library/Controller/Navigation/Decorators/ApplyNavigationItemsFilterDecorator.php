<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use WpService\Contracts\ApplyFilters;

class ApplyNavigationItemsFilterDecorator implements MenuItemsDecoratorInterface
{
    public function __construct(private ApplyFilters $wpService)
    {}
    /**
     * Decorates an array of menu items with a filter.
     *
     * This method applies a filter to an array of menu items, allowing customization of the menu items.
     *
     * @param array $menuItems The array of menu items to be decorated.
     * @param MenuConfigInterface $menuConfig The menu configuration object.
     * @return array The decorated array of menu items.
     */
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        return $this->wpService->applyFilters('Municipio/Navigation/Items', $menuItems, $menuConfig->getIdentifier());
    }
}