<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem\PageTreeFallback;

class AppendHrefDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    public function decorate(array|object $menuItem, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        $menuItem['href'] = get_permalink($menuItem['ID'], false);

        return $menuItem;
    }
}