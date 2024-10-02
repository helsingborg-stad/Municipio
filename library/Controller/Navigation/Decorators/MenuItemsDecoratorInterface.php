<?php

namespace Municipio\Controller\Navigation\Decorators;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

interface MenuItemsDecoratorInterface
{
    public function decorate(array $menuItems, MenuConfigInterface $menuConfig): array;
}
