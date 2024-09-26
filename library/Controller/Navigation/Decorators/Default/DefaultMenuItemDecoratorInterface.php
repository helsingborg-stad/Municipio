<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

interface DefaultMenuItemDecoratorInterface
{
    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig, array $ancestors): array;
}