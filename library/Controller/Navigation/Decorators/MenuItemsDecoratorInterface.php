<?php

namespace Municipio\Controller\Navigation\Decorators;

interface MenuItemsDecoratorInterface
{
    public function decorate(array $menuItems, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array;
}
