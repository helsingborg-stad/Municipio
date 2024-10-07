<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\NewMenuInterface;

interface MenuBuilderInterface
{
    // Default
    public function appendMenuItems(): void;
    public function mapMenuItemsFromObjectToArray(): void;
    public function mapMenuItemsAcfFieldValues(): void;
    public function mapMenuItemsIsAncestor(): void;
    public function applyMenuItemFilter(): void;

    // Breadcrumb
    public function appendHomeIconMenuItem(): void;
    public function appendArchiveMenuItem(): void;
    public function appendPageTreeAncestorsMenuItems(): void;
    public function applyBreadcrumbItemsFilter(): void;

    // Accessibility
    public function appendPrintMenuItem(): void;
    public function applyAccessibilityItemsFilter(): void;
    public function applyAccessibilityItemsDeprecatedFilter(): void;

    // Page tree
    public function pageTreeGetAncestors(): void;
    public function appendDataFromAncestorIds(): void;
    public function tryGetPageTreeMenuItemsFromCache(): void;
    public function pageTreeMenuItemsFormatter(): void;
    public function pageTreeAppendMenuItemsHref(): void;
    public function pageTreeAppendMenuItemsCustomTitle(): void;
    public function pageTreeAppendMenuItemsAncestors(): void;
    public function pageTreeAppendMenuItemIsCurrentPage(): void;
    public function pageTreeAppendChildren(): void;
    public function pageTreeSetMenuItemsCache(): void;

    // General
    public function applyMenuItemsFilter(): void;
    public function removeSubLevels(): void;
    public function removeTopLevel(): void;
    public function structureMenuItems(): void;
    public function applyNestedMenuItemsFilter(): void;

    // Get menu
    public function getMenu(): NewMenuInterface;
}
