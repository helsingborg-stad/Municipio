<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;

interface NewMenuInterface {
    public function getMenuItems(): array|false;
    public function getConfig(): NewMenuConfigInterface;
}