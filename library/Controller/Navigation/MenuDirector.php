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
        $this->builder->pageTreeSetMenuItemsCache();
        $this->builder->applyMenuItemFilter();
        $this->builder->applyMenuItemsFilter();
        $this->builder->structureMenuItems();
        $this->builder->removeSubLevels();
        $this->builder->removeTopLevel();
    }

    public function buildStandardMenuWithPageTreeFallback(): void
    {
        $this->builder->appendMenuItems();
        $menuItems = $this->builder->getMenu()->getMenuItems();
        empty($menuItems) ? $this->buildPageTreeMenu() : $this->buildStandardMenu();
    }
}
