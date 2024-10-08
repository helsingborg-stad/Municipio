<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\NewMenuInterface;
use Municipio\Controller\Navigation\FactoryInterface;

class NewMenu implements NewMenuInterface,FactoryInterface
{
    protected array $menu;

    public function __construct(
        private NewMenuConfigInterface $menuConfig,
        private array $menuItems = []
    ) {
        $this->menu = [];
        $this->menu['name'] = $this->menuConfig->getMenuName();
        $this->menu['identifier'] = $this->menuConfig->getIdentifier();
        $this->menu['items'] = [];
    }

    public function getMenuItems(): array
    {
        return $this->menu['items'];
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
