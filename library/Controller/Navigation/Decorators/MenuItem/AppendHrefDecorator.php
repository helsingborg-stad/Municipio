<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem;

class AppendHrefDecorator implements MenuItemDecoratorInterface
{
    public function decorate(array $menuItem): array
    {
        $menuItem['href'] = get_permalink($menuItem['ID'], false);

        return $menuItem;
    }
}