<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

interface MenuInterface
{
    public function createMenu(): array;
    public function getMenuNavItems(): array|false;
    public static function factory(MenuConfigInterface $menuConfig, array $decorators): self;
}
