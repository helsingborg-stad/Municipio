<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Helper\GetGlobal;

class Menu implements MenuInterface
{
    public function __construct(
        private MenuConfigInterface $menuConfig,
        private array $decorators = []
    ) {
    }

    public function createMenu(): array
    {
        $menu          = [];
        $menu['items'] = $this->getMenuNavItems();

        return $menu;
    }

    public function getMenuNavItems(): array|false
    {

        $menuItems = GetMenuData::getNavMenuItems($this->menuConfig->getMenuName()) ?: [];

        // Runs after the menu has been structured and complemented
        if (!empty($this->decorators)) {
            foreach ($this->decorators as $decorator) {
                $menuItems = $decorator->decorate($menuItems, $this->menuConfig);
            }
        }

        if (empty($menuItems)) {
            return false;
        }

        // Allows for final filtering
        return apply_filters('Municipio/Navigation/Nested', $menuItems, $this->menuConfig->getIdentifier(), $this->menuConfig->getMenuName());
    }

    public function getBreadcrumbItems(): array|false
    {
        if (!is_a(GetGlobal::getGlobal('post'), 'WP_Post')) {
            return false;
        }

        if (!empty($this->decorators)) {
            $menuItems = [];
            foreach ($this->decorators as $decorator) {
                $menuItems = $decorator->decorate($menuItems, $this->menuConfig);
            }

            return $menuItems;
        }

        return false;
    }

    public static function factory(MenuConfigInterface $menuConfig, array $decorators): self
    {
        return new self($menuConfig, $decorators);
    }
}
