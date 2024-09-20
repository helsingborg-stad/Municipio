<?php

namespace Municipio\Controller\Navigation\Decorators;

interface PageTreeDecoratorInterface
{
    public function decorate(array $menuItems): array;
}
