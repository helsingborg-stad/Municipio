<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Controller\Navigation\Config\MenuConfigInterface;

class Menu
{
    public function __construct(
        private MenuConfigInterface $menuConfig,
        private array $decorators = []
    ) {}
    
    public function createMenu(): array {
        $menu          = [];
        $menu['items'] = $this->getMenuNavItems();

        return $menu;
    }

    public function getMenuNavItems(): array|false {

        $menuItems = GetMenuData::getNavMenuItems($this->menuConfig->getMenuName()) ?: [];
        
        // Complements default menu items before structuring
        $menuItems = $this->menuConfig->getComplementDefaultDecoratorInstance()->decorate($menuItems, $this->menuConfig);

        // Complements page tree fallback
        $menuItems = $this->menuConfig->getComplementPageTreeDecoratorInstance()->decorate($menuItems, $this->menuConfig);

        // Structures the complemented menu items
        $menuItems = $this->menuConfig->getStructureMenuItemsDecoratorInstance()->decorate($menuItems, $this->menuConfig);

        // Allow for filtering after the early decorators
        $menuItems = apply_filters('Municipio/Navigation/Items', $menuItems, $this->menuConfig->getIdentifier());

        if (empty($menuItems)) {
            return false;
        }

        // Runs after the menu has been structured and complemented
        if (!empty($this->decorators)) {
            foreach ($this->decorators as $decorator) {
                $menuItems = $decorator->decorate($menuItems, $this->menuConfig);
            }
        }

        // Allows for final filtering
        return apply_filters('Municipio/Navigation/Nested', $menuItems, $this->menuConfig->getIdentifier(), $this->menuConfig->getMenuName());
    }

    public static function createMenu()
}
