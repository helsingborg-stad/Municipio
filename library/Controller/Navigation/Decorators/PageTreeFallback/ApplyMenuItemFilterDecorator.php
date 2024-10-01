<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use WpService\Contracts\ApplyFilters;

class ApplyMenuItemFilterDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    public function __construct(private ApplyFilters $wpService)
    {
    }
    /**
     * Decorates a menu item by applying a filter.
     *
     * @param array $menuItem The menu item to be decorated.
     * @param MenuConfigInterface $menuConfig The menu configuration.
     * @param ComplementPageTreeDecorator $parentInstance The parent instance of the decorator.
     * @return array The decorated menu item.
     */
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig, ComplementPageTreeDecorator $parentInstance): array
    {
        return $this->wpService->applyFilters('Municipio/Navigation/Item', $menuItem, $menuConfig->getIdentifier(), true);
    }
}
