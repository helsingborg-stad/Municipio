<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Controller\Navigation\FactoryInterface;

class Menu implements MenuInterface, FactoryInterface
{
    public array $menu;

    public function __construct(
        private MenuConfigInterface $menuConfig,
    ) {
        $this->menu               = [];
        $this->menu['name']       = $this->menuConfig->getMenuName();
        $this->menu['identifier'] = $this->menuConfig->getIdentifier();
        $this->menu['items']      = [];
    }

    /**
     * Retrieves the menu.
     *
     * @return array The menu.
     */
    public function getMenu(): array
    {
        return $this->menu;
    }

    /**
     * Retrieves the menu configuration.
     *
     * @return MenuConfigInterface The menu configuration.
     */
    public function getConfig(): MenuConfigInterface
    {
        return $this->menuConfig;
    }

    /**
     * Factory method.
     *
     * @param MenuConfigInterface $menuConfig The menu configuration.
     *
     * @return self The menu.
     */
    public static function factory(MenuConfigInterface $menuConfig): self
    {
        return new self($menuConfig);
    }
}
