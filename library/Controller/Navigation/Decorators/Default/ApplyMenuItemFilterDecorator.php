<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

class ApplyMenuItemFilterDecorator implements DefaultMenuItemDecoratorInterface
{
    public function __construct(private string $identifier)
    {}

    public function decorate(array|object $menuItem, array $ancestors): array
    {
        return apply_filters('Municipio/Navigation/Item', $menuItem, $this->identifier, true);
    }
}