<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Helper\Navigation as NavigationHelperInstance;
use Municipio\Helper\Navigation\MenuConstructor as MenuConstructorInstance;
use Municipio\Controller\Navigation\Decorators\MenuItemsDecoratorInterface;

class Menu
{
    private string|int $localeIdentifier;
    private static $db;

    public function __construct(
        private NavigationHelperInstance $navigationHelperInstance,
        private MenuConstructorInstance $menuConstructorInstance,
        private array $complementMenuItemsDecorator,
        private string $identifier = '',
        private ?int $menuId = null,
        private ?string $menuName = null,
        private ?int $pageId = null,
        private string $context = 'municipio'
    ) {
        $this->localeIdentifier = $this->menuName ?: $this->menuId ?: $this->identifier;
        $this->globalToLocal('wpdb', 'db');
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
        foreach ($this->complementMenuItemsDecorator as $decorator) {
            $menuItems = $decorator->decorate(
                $menuItems, 
                $fallbackToPageTree, 
                $includeTopLevel, 
                $onlyKeepFirstLevel
            );
        }

        return $menuItems;
    }

    /**
     * Creates a local copy of the global instance
     * The target var should be defined in class header as private or public
     *
     * @param string $global The name of global varable that should be made local
     * @param string $local Handle the global with the name of this string locally
     *
     * @return void
     */
    private function globalToLocal($global, $local = null)
    {
        global $$global;
        if (is_null($local)) {
            self::$$global = $$global;
        } else {
            self::$$local = $$global;
        }
    }
}
