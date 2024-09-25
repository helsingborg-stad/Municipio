<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

class AppendIsAncestorDecorator implements DefaultMenuItemDecoratorInterface
{
    public function decorate(array|object $menuItem, array $ancestors): array
    {
        if (!isset($menuItem['id']) || empty($ancestors)) {
            return $menuItem;
        }

        $menuItem['ancestor'] = in_array($menuItem['id'], $ancestors);

        return $menuItem;
    }
}