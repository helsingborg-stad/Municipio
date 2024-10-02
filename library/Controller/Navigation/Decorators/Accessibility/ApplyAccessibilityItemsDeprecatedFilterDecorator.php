<?php

namespace Municipio\Controller\Navigation\Decorators\Accessibility;

use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class ApplyAccessibilityItemsDeprecatedFilterDecorator implements MenuItemsDecoratorInterface
{
    public function __construct()
    {
    }

    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array
    {
        return apply_filters_deprecated('accessibility_items', [$menuItems], '3.0.0', 'Municipio/Accessibility/Items');
    }
}