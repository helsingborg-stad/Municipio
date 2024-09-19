<?php

namespace Municipio\Controller\Navigation\Decorators;

interface MenuItemsDecoratorInterface
{
    public function decorate(array $menuItems): array;
}
