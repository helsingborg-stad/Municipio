<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;
use Municipio\Helper\Navigation;
use Municipio\Helper\Navigation\MenuConstructor;

class Menu
{
    public function __construct(
        private Navigation $navigationHelperInstance,
        private array $decorators,
        private string $identifier = '',
        private ?int $menuId = null,
        private ?string $menuName = null,
        private ?int $pageId = null,
        private string $context = 'municipio',
    ) {
    }

    public function setMenuName($menuName) {
        $this->menuName = $menuName;

        return $this;
    }

    public function setDecorators($decorators) {
        $this->decorators = $decorators;

        return $this;
    }

    public function setIdentifier($identifier) {
        $this->identifier = $identifier;

        return $this;
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

        if (!empty($this->decorators)) {
            foreach ($this->decorators as $decorator) {
                $menuItems = $decorator->decorate($menuItems, $fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);
            }
        }

        $menuItems = apply_filters('Municipio/Navigation/Items', $menuItems, $this->identifier);

        if (empty($menuItems)) {
            return false;
        }


        

        return $menuItems;
    }
}
