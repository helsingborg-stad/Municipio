<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;
use Municipio\Controller\Navigation\FactoryInterface;

class BreadcrumbMenu implements NewMenuInterface,FactoryInterface
{
    public function __construct(
        private NewMenuConfigInterface $menuConfig,
        private array $menuItems = []
    ) {
    }

    public function getMenuItems(): array
    {
        return $this->menuItems;
    }

    public function getConfig(): NewMenuConfigInterface
    {
        return $this->menuConfig;
    }

    public static function factory(NewMenuConfigInterface $menuConfig): self
    {
        return new self($menuConfig);
    }
}
