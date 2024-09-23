<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Helper\Navigation;
use Municipio\Helper\Navigation\MenuConstructor;
use Municipio\Controller\Navigation\Decorators\ComplementMenuItemsDecorator;
use Municipio\Controller\Navigation\Decorators\PageTreeFallbackDecorator;

class Menu
{
    private string|int $localeIdentifier;

    public function __construct(
        private Navigation $navigationHelperInstance,
        private MenuConstructor $menuConstructorInstance,
        private ComplementMenuItemsDecorator $complementMenuItemsDecoratorInstance,
        private PageTreeFallbackDecorator $pageTreeFallbackDecoratorInstance,
        private array $defaultMenuItemsDecorators,
        private string $identifier = '',
        private ?int $menuId = null,
        private ?string $menuName = null,
        private ?int $pageId = null,
        private string $context = 'municipio',
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
        $menuItems = $this->complementMenuItemsDecoratorInstance->decorate(
            $menuItems, 
            $fallbackToPageTree, 
            $includeTopLevel, 
            $onlyKeepFirstLevel
        );

        $menuItems = $this->pageTreeFallbackDecoratorInstance->decorate(
            $menuItems, 
            $fallbackToPageTree, 
            $includeTopLevel, 
            $onlyKeepFirstLevel
        );

        return $menuItems;
    }
}
