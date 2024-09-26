<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class ApplyMenuItemFilterDecorator implements DefaultMenuItemDecoratorInterface
{
    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig, array $ancestors): array
    {
        return apply_filters('Municipio/Navigation/Item', $menuItem, $menuConfig->getIdentifier(), true);
    }
}