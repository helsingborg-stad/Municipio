<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class ApplyNavigationItemsFilterDecorator implements MenuItemsDecoratorInterface
{
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        return apply_filters('Municipio/Navigation/Items', $menuItems, $menuConfig->getIdentifier());
    }
}