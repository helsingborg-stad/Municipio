<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

interface MenuInterface {
    public function getMenu(): array;
    public function getConfig(): MenuConfigInterface;
}