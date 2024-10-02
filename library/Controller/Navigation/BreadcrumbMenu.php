<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Helper\GetGlobal;

class BreadcrumbMenu implements MenuInterface
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
