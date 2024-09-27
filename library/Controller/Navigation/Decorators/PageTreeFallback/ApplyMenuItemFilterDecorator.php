<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class ApplyMenuItemFilterDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
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
        return apply_filters('Municipio/Navigation/Item', $menuItem, $menuConfig->getIdentifier(), true);
    }
}
