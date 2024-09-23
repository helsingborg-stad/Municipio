<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItems;

interface PageTreeDecoratorInterface
{
    public function decorate(array $menuItems): array;
}
