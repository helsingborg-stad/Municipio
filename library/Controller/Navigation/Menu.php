<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Controller\Navigation\FactoryInterface;

class Menu implements MenuInterface,FactoryInterface
{
    protected array $menu;

    public function __construct(
        private MenuConfigInterface $menuConfig,
        private array $menuItems = []
    ) {
        $this->menu = [];
        $this->menu['name'] = $this->menuConfig->getMenuName();
        $this->menu['identifier'] = $this->menuConfig->getIdentifier();
        $this->menu['items'] = [];
    }

    public function getMenu(): array
    {
        return $this->menu;
    }

    public function getConfig(): MenuConfigInterface
    {
        return $this->menuConfig;
    }

    public static function factory(MenuConfigInterface $menuConfig): self
    {
        return new self($menuConfig);
    }
}
