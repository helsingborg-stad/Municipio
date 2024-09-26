<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class ApplyMenuItemFilterDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    public function __construct()
    {}

    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig): array
    {
        return apply_filters('Municipio/Navigation/Item', $menuItem, $menuConfig->getIdentifier(), true);
    }
}
