<?php

namespace Municipio\Controller\Navigation\Decorators\MenuItem\Default;

interface DefaultMenuItemDecoratorInterface
{
    public function decorate(array|object $menuItem, array $ancestors): array;
}