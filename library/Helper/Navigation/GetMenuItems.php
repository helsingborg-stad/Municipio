<?php

namespace Municipio\Helper\Navigation;

use Municipio\Helper\Navigation;
use Municipio\Helper\Navigation\MenuConstructor;

class GetMenuItems
{
    public function __construct(
        private Navigation $navigationInstance, 
        private MenuConstructor $menuConstructorInstance,
        private int|string $menu, 
        private ?int $pageId = null,
        private bool $fallbackToPageTree = false, 
        private bool $includeTopLevel = true, 
        private bool $onlyKeepFirstLevel = false
    ) {
    }

     /**
     * Get WordPress menu items (from default menu management)
     *
     * @param string $menu The menu id to get
     * @return bool|array
     */
    public function getMenuItems()
    {
        $menuItems = GetMenuData::getNavMenuItems($this->menu) ?: [];
        $menuItems = $this->menuConstructorInstance->structureMenuItems($menuItems, $this->pageId);

        if (empty($menuItems) && $this->fallbackToPageTree && is_numeric($this->pageId)) {
            $menuItems = $this->navigationInstance->getNested($this->pageId);
        }

        $menuItems = apply_filters('Municipio/Navigation/Items', $menuItems, $this->menu);

        if (!empty($menuItems)) {
            $pageStructure = $this->includeTopLevel ?
                $this->menuConstructorInstance->buildStructuredMenu($menuItems) :
                $this->navigationInstance->removeTopLevel($this->menuConstructorInstance->buildStructuredMenu($menuItems));

            if ($this->onlyKeepFirstLevel) {
                $pageStructure = $this->navigationInstance->removeSubLevels($pageStructure);
            }


            $menuItems = apply_filters('Municipio/Navigation/Nested', $pageStructure, $this->menu, $this->pageId);

            if ($this->menu === 'secondary-menu' && !empty($menuItems)) {
                $menuItems = $this->menuConstructorInstance->structureMenu($menuItems, $this->menu);
            }

            return $menuItems;
        }

        return false;
    }
}