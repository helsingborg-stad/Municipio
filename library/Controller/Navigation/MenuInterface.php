<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

interface MenuInterface {
    public function getMenu(): array;
    public function getMenuItems(): array|false;
    public function getConfig(): MenuConfigInterface;
}