<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

interface FactoryInterface
{
    public static function factory(MenuConfigInterface $menuConfig): self;
}