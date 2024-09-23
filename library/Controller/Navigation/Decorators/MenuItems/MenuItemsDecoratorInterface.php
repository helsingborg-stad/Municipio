<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItems;

interface MenuItemsDecoratorInterface
{
    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array;
}
