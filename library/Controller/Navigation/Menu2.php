<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Controller\Navigation\Decorators\StructureMenuItemsDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\ComplementPageTreeDecorator;
use Municipio\Controller\Navigation\Decorators\Default\ComplementDefaultDecorator;
use Municipio\Controller\Navigation\MenuConfigInterface;

class Menu2
{
    public function __construct(
        private MenuConfigInterface $menuConfig,
        private StructureMenuItemsDecorator $structureMenuItemsDecoratorInstance,
        private ComplementDefaultDecorator $complementDefaultDecoratorInstance,
        private ComplementPageTreeDecorator $complementPageTreeDecoratorInstance,
        private array $decorators = []
    ) {}
    
    public function createMenu(): array {
        $menu          = [];
        $menu['items'] = $this->getMenuNavItems();

        return $menu;
    }

    public function getMenuNavItems(
        bool $fallbackToPageTree = false,
        bool $includeTopLevel = true,
        bool $onlyKeepFirstLevel = false,
        string $menuName = null
    ): array|false {

        $menuItems = GetMenuData::getNavMenuItems($this->menuConfig->getMenuName()) ?: [];
        
        // Complements default menu items before structuring
        $menuItems = $this->complementDefaultDecoratorInstance->decorate($menuItems, $this->menuConfig);

        // Complements page tree fallback
        $menuItems = $this->complementPageTreeDecoratorInstance->decorate($menuItems, $this->menuConfig);

        // Structures the complemented menu items
        $menuItems = $this->structureMenuItemsDecoratorInstance->decorate($menuItems, $this->menuConfig);

        // Allow for filtering after the early decorators
        $menuItems = apply_filters('Municipio/Navigation/Items', $menuItems, $this->menuConfig->getIdentifier());

        if (empty($menuItems)) {
            return false;
        }

        // Runs after the menu has been structured and complemented
        if (!empty($this->decorators)) {
            foreach ($this->decorators as $decorator) {
                $menuItems = $decorator->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);
            }
        }

        // Allows for final filtering
        return apply_filters('Municipio/Navigation/Nested', $menuItems, $this->identifier, $this->pageId);
    }
}
