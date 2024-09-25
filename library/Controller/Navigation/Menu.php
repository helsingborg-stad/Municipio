<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Controller\Navigation\Decorators\MenuItems\StructureMenuItemsDecorator;
use Municipio\Controller\Navigation\Decorators\MenuItems\PageTreeFallback\ComplementPageTreeMenuItemsDecorator;
use Municipio\Controller\Navigation\Decorators\MenuItems\Default\ComplementDefaultMenuItemsDecorator;

class Menu
{
    public function __construct(
        private StructureMenuItemsDecorator $structureMenuItemsDecoratorInstance,
        private ComplementDefaultMenuItemsDecorator $complementDefaultMenuItemsDecoratorInstance,
        private ComplementPageTreeMenuItemsDecorator $complementPageTreeMenuItemsDecoratorInstance,
        private string $identifier = '',
        private ?string $menuName = null,
        private ?int $pageId = null,
        private string $context = 'municipio',
        private array $decorators = []
    ) {}

    public static function factory(StructureMenuItemsDecorator $structureMenuItemsDecoratorInstance, ComplementDefaultMenuItemsDecorator $complementDefaultMenuItemsDecoratorInstance, ComplementPageTreeMenuItemsDecorator $complementPageTreeMenuItemsDecoratorInstance, string $identifier = '', ?string $menuName = null, ?int $pageId = null, string $context = 'municipio', array $decorators = []): self
    {
        return new self($structureMenuItemsDecoratorInstance, $complementDefaultMenuItemsDecoratorInstance, $complementPageTreeMenuItemsDecoratorInstance, $identifier, $menuName, $pageId, $context, $decorators);
    }
    
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
        $menuItems = $this->complementDefaultMenuItemsDecoratorInstance->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);

        // Complements page tree fallback
        $menuItems = $this->complementPageTreeMenuItemsDecoratorInstance->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);

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
