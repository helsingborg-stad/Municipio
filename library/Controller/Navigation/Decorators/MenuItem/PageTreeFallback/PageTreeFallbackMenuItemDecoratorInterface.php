<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem\PageTreeFallback;

interface PageTreeFallbackMenuItemDecoratorInterface
{
    public function decorate(array $menuItem, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array;
}