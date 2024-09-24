<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Controller\Navigation\Decorators\MenuItems\StructureMenuItemsDecorator;

class Menu
{
    public function __construct(
        private string $identifier = '',
        private ?string $menuName = null,
        private ?int $pageId = null,
        private string $context = 'municipio',
        private StructureMenuItemsDecorator $structureMenuItemsDecoratorInstance,
        private array $earlyDecorators = [],
        private array $lateDecorators = []
    ) {}

    public function setMenuName(string $menuName) 
    {
        $this->menuName = $menuName;
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
        bool $onlyKeepFirstLevel = false
    ): array|false {

        $menuItems = GetMenuData::getNavMenuItems($this->menuName) ?: [];

        // Runs before the menu has been structured
        if (!empty($this->earlyDecorators)) {
            foreach ($this->earlyDecorators as $decorator) {
                $menuItems = $decorator->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);
            }
        }
        // Allow for filtering after the early decorators
        $menuItems = apply_filters('Municipio/Navigation/Items', $this->structureMenuItemsDecoratorInstance->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel), $this->identifier);

        if (empty($menuItems)) {
            return false;
        }

        // Runs after the menu has been structured
        if (!empty($this->lateDecorators)) {
            foreach ($this->lateDecorators as $decorator) {
                $menuItems = $decorator->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);
            }
        }

        // Allows for final filtering
        return apply_filters('Municipio/Navigation/Nested', $menuItems, $this->identifier, $this->pageId);
    }
}