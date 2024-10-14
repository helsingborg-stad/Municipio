<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\MenuBuilderInterface;

class MenuDirector
{
    private MenuBuilderInterface $builder;

    public function setBuilder(MenuBuilderInterface $builder): void
    {
        $this->builder = $builder;
    }

    public function buildAccessibilityMenu(): void
    {
        $this->builder->appendPrintMenuItem();
        $this->builder->applyAccessibilityItemsFilter();
        $this->builder->applyAccessibilityItemsDeprecatedFilter();
    }

    public function buildBreadcrumbMenu(): void
    {
        $this->builder->appendHomeIconMenuItem();
        $this->builder->appendArchiveMenuItem();
        $this->builder->appendPageTreeAncestorsMenuItems();
        $this->builder->applyBreadcrumbItemsFilter();
    }

    public function buildStandardMenu(): void
    {
        $this->builder->appendMenuItems();
        $this->builder->appendAcfFields();
        $this->builder->mapMenuItemsFromObjectToArray();
        $this->builder->mapMenuItemsAcfFieldValues();
        $this->builder->mapMenuItemsIsAncestor();
        $this->builder->applyMenuItemFilter();
        $this->builder->applyMenuItemsFilter();
        $this->builder->structureMenuItems();
        $this->builder->removeSubLevels();
        $this->builder->removeTopLevel();
        $this->builder->applyNestedMenuItemsFilter();
    }

    public function buildPageTreeMenu(): void
    {
        $this->builder->pageTreeGetAncestors();
        $this->builder->appendDataFromAncestorIds();
        $this->builder->tryGetPageTreeMenuItemsFromCache();
        $this->builder->pageTreeMenuItemsFormatter();
        $this->builder->pageTreeAppendMenuItemsHref();
        $this->builder->pageTreeAppendMenuItemsCustomTitle();
        $this->builder->pageTreeAppendMenuItemIsCurrentPage();
        $this->builder->pageTreeAppendMenuItemsAncestors();
        $this->builder->pageTreeAppendChildren();
        $this->builder->applyMenuItemFilter();
        // Note: No more changes to the individual menu items after this point
        $this->builder->pageTreeSetMenuItemsCache();
        // The following methods are used to structure the menu as a whole
        $this->builder->applyMenuItemsFilter();
        $this->builder->structureMenuItems();
        $this->builder->removeSubLevels();
        $this->builder->removeTopLevel();
    }

    public function buildStandardMenuWithPageTreeSubitems(): void
    {
        $this->builder->appendMenuItems();
        $this->builder->appendAcfFields();
        $this->builder->mapMenuItemsFromObjectToArray();
        $this->builder->mapMenuItemsAcfFieldValues();
        $this->builder->convertStaticMenuItemsToPageTreeItems();
        $this->builder->pageTreeAppendChildren();
        $this->builder->applyMenuItemFilter();
        $this->builder->applyMenuItemsFilter();
        $this->builder->structureMenuItems();
        $this->builder->removeSubLevels();
        $this->builder->removeTopLevel();
        $this->builder->applyNestedMenuItemsFilter();
    }

    public function buildStandardMenuWithPageTreeFallback(): void
    {
        $this->builder->appendMenuItems();
        $menu = $this->builder->getMenu()->getMenu();
        empty($menu['items']) ? $this->buildPageTreeMenu() : $this->buildStandardMenu();
    }
}
