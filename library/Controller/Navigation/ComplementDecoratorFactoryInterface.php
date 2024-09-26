<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;

Interface ComplementDecoratorFactoryInterface {
    public function createComplementDecorator(): MenuItemsDecoratorInterface;

    public function setDecorators(array $decorators): void;

    public function getDecorators(): array;
}