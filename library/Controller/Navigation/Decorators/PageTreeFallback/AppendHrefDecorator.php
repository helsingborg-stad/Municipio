<?php

namespace Municipio\Controller\Navigation\Decorators\PageTreeFallback;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class AppendHrefDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    public function decorate(array $menuItem, MenuConfigInterface $menuConfig, ComplementPageTreeDecorator $parentInstance): array
    {
        $menuItem['href'] = get_permalink($menuItem['id'], false);

        return $menuItem;
    }
}