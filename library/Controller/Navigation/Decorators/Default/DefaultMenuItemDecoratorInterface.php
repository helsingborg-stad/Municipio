<?php

namespace Municipio\Controller\Navigation\Decorators\Default;

interface DefaultMenuItemDecoratorInterface
{
    public function decorate(array|object $menuItem, array $ancestors): array;
}