<?php

namespace Municipio\Controller\Navigation;

use Municipio\Helper\Navigation\GetMenuData as GetMenuData;

class Menu
{
    private string|int $localeIdentifier;

    public function __construct(
        private array $decorators,
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
    ): array|false {

        $menuItems = GetMenuData::getNavMenuItems($this->localeIdentifier) ?: [];

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
