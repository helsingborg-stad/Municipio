<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\MenuConfigInterface;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;

Interface ComplementDecoratorFactoryInterface {
    public function __construct(MenuConfigInterface $menuConfig);

    public function createComplementDecorator(): MenuItemsDecoratorInterface;

    public function setDecorators(array $decorators): void;

    public function getDecorators(): array;
}