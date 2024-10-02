<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class AccessibilityMenu implements MenuInterface
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
        $menuItems = [];

        if (!empty($this->decorators)) {
            foreach ($this->decorators as $decorator) {
                $menuItems = $decorator->decorate($menuItems, $this->menuConfig);
            }
        }

        return $menuItems;
    }

    public static function factory(MenuConfigInterface $menuConfig, array $decorators): self
    {
        return new self($menuConfig, $decorators);
    }
}
