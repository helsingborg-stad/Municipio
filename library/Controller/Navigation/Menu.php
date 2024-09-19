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
    ) {
        $menu          = [];
        $menu['items'] = $this->getMenuNavItems($fallbackToPageTree, $includeTopLevel, $onlyKeepFirstLevel);
    }

    private function getMenuNavItems(
        bool $fallbackToPageTree = false,
        bool $includeTopLevel = true,
        bool $onlyKeepFirstLevel = false
    ) {
        $menuItems = GetMenuData::getNavMenuItems($this->localeIdentifier) ?: [];
        // $menuItems =
    }

         /**
     * Get WordPress menu items (from default menu management)
     *
     * @param string $menu The menu id to get
     * @return bool|array
     */
    private function getMenuItems(
        bool $fallbackToPageTree = false,
        bool $includeTopLevel = true,
        bool $onlyKeepFirstLevel = false
    ) {
        $localeIdentifier = $this->menuName ?: $this->menuId ?: $this->identifier;

        $menuItems = GetMenuData::getNavMenuItems($localeIdentifier) ?: [];
        $menuItems = $this->menuConstructorInstance->structureMenuItems($menuItems, $this->pageId);

        if (empty($menuItems) && $fallbackToPageTree && is_numeric($this->pageId)) {
            $menuItems = $this->navigationHelperInstance->getNested($this->pageId);
        }

        $menuItems = apply_filters('Municipio/Navigation/Items', $menuItems, $localeIdentifier);

        if (!empty($menuItems)) {
            $pageStructure = $includeTopLevel ?
                $this->menuConstructorInstance->buildStructuredMenu($menuItems) :
                $this->navigationHelperInstance->removeTopLevel($this->menuConstructorInstance->buildStructuredMenu($menuItems));

            if ($onlyKeepFirstLevel) {
                $pageStructure = $this->navigationHelperInstance->removeSubLevels($pageStructure);
            }


            $menuItems = apply_filters('Municipio/Navigation/Nested', $pageStructure, $localeIdentifier, $this->pageId);

            // if ($this->menu === 'secondary-menu' && !empty($menuItems)) {
            //     $menuItems = $this->menuConstructorInstance->structureMenu($menuItems, $this->menu);
            // }

            return $menuItems;
        }

        return false;
    }
}
