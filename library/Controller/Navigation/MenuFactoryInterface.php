<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\MenuConfigInterface;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;
use Municipio\Controller\Navigation\Menu;

Interface MenuFactoryInterface {
    public function __construct(MenuConfigInterface $menuConfig);

    public function createMenu(): Menu;

    public function setDecorators(MenuItemsDecoratorInterface ...$vars): void;

    public function getDecorators(): array;
}