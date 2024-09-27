<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Helper\GetAncestorIds;

class AppendIsAncestorDecorator implements DefaultMenuItemDecoratorInterface
{
    public function decorate(array|object $menuItem, MenuConfigInterface $menuConfig, array $ancestors): array
    {
        if (!isset($menuItem['id']) || empty($ancestors)) {
            return $menuItem;
        }

        $menuItem['ancestor'] = in_array($menuItem['id'], $ancestors);

        return $menuItem;
    }
}