<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem\PageTreeFallback;

class ApplyMenuItemFilterDecorator implements PageTreeFallbackMenuItemDecoratorInterface
{
    public function __construct(private string $identifier)
    {}

    public function decorate(array|object $menuItem, bool $fallbackToPageTree, bool $includeTopLevel, bool $onlyKeepFirstLevel): array
    {
        return apply_filters('Municipio/Navigation/Item', $menuItem, $this->identifier, true);
    }
}
