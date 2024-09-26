<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

interface PageTreeFallbackMenuItemDecoratorInterface
{
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig): array;
}