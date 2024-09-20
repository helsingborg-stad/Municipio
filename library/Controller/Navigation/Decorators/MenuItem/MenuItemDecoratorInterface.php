<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem;

interface MenuItemDecoratorInterface
{
    public function decorate(array $menuItem): array;
}