<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Helper\Navigation as NavigationHelperInstance;
use Municipio\Helper\Navigation\MenuConstructor as MenuConstructorInstance;

class Menu
{
    private string|int $localeIdentifier;

    public function __construct(
        private NavigationHelperInstance $navigationHelperInstance,
        private MenuConstructorInstance $menuConstructorInstance,
        private array $defaultMenuItemsDecorators,
        private string $identifier = '',
        private ?int $menuId = null,
        private ?string $menuName = null,
        private ?int $pageId = null,
        private string $context = 'municipio'
    ) {
        $this->localeIdentifier = $this->menuName ?: $this->menuId ?: $this->identifier;
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
    ): array {
        $menuItems = GetMenuData::getNavMenuItems($this->localeIdentifier) ?: [];
        foreach ($this->defaultMenuItemsDecorators as $decorator) {
            $menuItems = $decorator->decorate(
                $menuItems, 
                $fallbackToPageTree, 
                $includeTopLevel, 
                $onlyKeepFirstLevel
            );
        }

        return $menuItems;
    }
}
