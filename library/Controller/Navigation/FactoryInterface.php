<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;

interface FactoryInterface
{
    public static function factory(NewMenuConfigInterface $menuConfig): self;
}