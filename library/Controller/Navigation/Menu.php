<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Controller\Navigation\Decorators\StructureMenuItemsDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallback\ComplementPageTreeDecorator;
use Municipio\Controller\Navigation\Decorators\Default\ComplementDefaultDecorator;

class Menu
{
    public function __construct(
        private StructureMenuItemsDecorator $structureMenuItemsDecoratorInstance,
        private ComplementDefaultDecorator $complementDefaultDecoratorInstance,
        private ComplementPageTreeDecorator $complementPageTreeDecoratorInstance,
        private string $identifier = '',
        private ?string $menuName = null,
        private ?int $pageId = null,
        private string $context = 'municipio',
        private array $decorators = []
    ) {}
    
    public function createMenu(
        bool $fallbackToPageTree = false,
        bool $includeTopLevel = true,
        bool $onlyKeepFirstLevel = false
    ): array {
        $menu          = [];
        $menu['items'] = $this->getMenuNavItems($fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);

        return $menu;
    }

    public function getMenuNavItems(
        bool $fallbackToPageTree = false,
        bool $includeTopLevel = true,
        bool $onlyKeepFirstLevel = false,
        string $menuName = null
    ): array|false {

        $menuItems = GetMenuData::getNavMenuItems($menuName ?: $this->menuName) ?: [];
        
        // Complements default menu items before structuring
        $menuItems = $this->complementDefaultDecoratorInstance->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);

        // Complements page tree fallback
        $menuItems = $this->complementPageTreeDecoratorInstance->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);

        // Structures the complemented menu items
        $menuItems = $this->structureMenuItemsDecoratorInstance->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);

        // Allow for filtering after the early decorators
        $menuItems = apply_filters('Municipio/Navigation/Items', $menuItems, $this->identifier);

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
