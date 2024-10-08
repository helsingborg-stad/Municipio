<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\NewMenuConfigInterface;
use Municipio\Controller\Navigation\Decorators\Accessibility\AppendPrintMenuItem;
use Municipio\Controller\Navigation\Decorators\Accessibility\ApplyAccessibilityItemsDeprecatedFilter;
use Municipio\Controller\Navigation\Decorators\Accessibility\ApplyAccessibilityItemsFilter;
use Municipio\Controller\Navigation\Decorators\Breadcrumb\AppendArchiveMenuItem;
use Municipio\Controller\Navigation\Decorators\Breadcrumb\AppendHomeIconMenuItem;
use Municipio\Controller\Navigation\Decorators\Breadcrumb\AppendPageTreeAncestorsMenuItems;
use Municipio\Controller\Navigation\Decorators\Breadcrumb\ApplyBreadcrumbItemsFilter;
use Municipio\Controller\Navigation\Decorators\NewMenu\AppendDataFromAncestorIds;
use Municipio\Controller\Navigation\Decorators\NewMenu\PageTreeAppendMenuItemIsCurrentPage;
use Municipio\Controller\Navigation\Decorators\NewMenu\AppendMenuItems;
use Municipio\Controller\Navigation\Decorators\NewMenu\ApplyMenuItemFilter;
use Municipio\Controller\Navigation\Decorators\NewMenu\ApplyMenuItemsFilter;
use Municipio\Controller\Navigation\Decorators\NewMenu\ApplyNestedMenuItemsFilter;
use Municipio\Controller\Navigation\Decorators\NewMenu\PageTreeGetAncestors;
use Municipio\Controller\Navigation\Decorators\NewMenu\MapMenuItemsAcfFieldValues;
use Municipio\Controller\Navigation\Decorators\NewMenu\MapMenuItemsFromObjectToArray;
use Municipio\Controller\Navigation\Decorators\NewMenu\MapMenuItemsIsAncestor;
use Municipio\Controller\Navigation\Decorators\NewMenu\PageTreeAppendChildren;
use Municipio\Controller\Navigation\Decorators\NewMenu\PageTreeAppendMenuItemsAncestors;
use Municipio\Controller\Navigation\Decorators\NewMenu\PageTreeAppendMenuItemsCustomTitle;
use Municipio\Controller\Navigation\Decorators\NewMenu\PageTreeAppendMenuItemsHref;
use Municipio\Controller\Navigation\Decorators\NewMenu\PageTreeMenuItemsFormatter;
use Municipio\Controller\Navigation\Decorators\NewMenu\PageTreeSetMenuItemsCache;
use Municipio\Controller\Navigation\Decorators\NewMenu\RemoveSubLevels;
use Municipio\Controller\Navigation\Decorators\NewMenu\RemoveTopLevel;
use Municipio\Controller\Navigation\Decorators\NewMenu\StructureMenuItems;
use Municipio\Controller\Navigation\Decorators\NewMenu\TryGetPageTreeMenuItemsFromCache;
use Municipio\Controller\Navigation\NewMenuInterface;
use Municipio\Controller\Navigation\MenuBuilderInterface;

class MenuBuilder implements MenuBuilderInterface
{
    private $menu;

    public function __construct(private NewMenuConfigInterface $menuConfig, private $acfService, private $wpService) {
        $this->menu = NewMenu::factory($this->menuConfig);
    }

    public function appendMenuItems(): void
    {
        $this->menu = new AppendMenuItems($this->menu);
    }

    public function mapMenuItemsFromObjectToArray(): void
    {
        $this->menu = new MapMenuItemsFromObjectToArray($this->menu);
    }

    public function mapMenuItemsAcfFieldValues(): void
    {
        $this->menu = new MapMenuItemsAcfFieldValues($this->menu, $this->acfService);
    }

    public function mapMenuItemsIsAncestor(): void
    {
        $this->menu = new MapMenuItemsIsAncestor($this->menu);
    }

    public function applyMenuItemFilter(): void
    {
        $this->menu = new ApplyMenuItemFilter($this->menu, $this->wpService);
    }

    public function appendHomeIconMenuItem(): void
    {
        $this->menu = new AppendHomeIconMenuItem($this->menu, $this->wpService);
    }

    public function appendArchiveMenuItem(): void
    {
        $this->menu = new AppendArchiveMenuItem($this->menu, $this->wpService);
    }

    public function appendPageTreeAncestorsMenuItems(): void
    {
        $this->menu = new AppendPageTreeAncestorsMenuItems($this->menu);
    }

    public function applyBreadcrumbItemsFilter(): void
    {
        $this->menu = new ApplyBreadcrumbItemsFilter($this->menu, $this->wpService);
    }

    public function appendPrintMenuItem(): void
    {
        $this->menu = new AppendPrintMenuItem($this->menu);
    }

    public function applyAccessibilityItemsDeprecatedFilter(): void
    {
        $this->menu = new ApplyAccessibilityItemsDeprecatedFilter($this->menu, $this->wpService);
    }

    public function applyAccessibilityItemsFilter(): void
    {
        $this->menu = new ApplyAccessibilityItemsFilter($this->menu, $this->wpService);
    }

    public function applyMenuItemsFilter(): void
    {
        $this->menu = new ApplyMenuItemsFilter($this->menu, $this->wpService);
    }

    public function removeSubLevels(): void
    {
        $this->menu = new RemoveSubLevels($this->menu);
    }

    public function removeTopLevel(): void
    {
        $this->menu = new RemoveTopLevel($this->menu);
    }

    public function structureMenuItems(): void
    {
        $this->menu = new StructureMenuItems($this->menu);
    }

    public function applyNestedMenuItemsFilter(): void
    {
        $this->menu = new ApplyNestedMenuItemsFilter($this->menu, $this->wpService);
    }

    public function pageTreeGetAncestors(): void
    {
        $this->menu = new PageTreeGetAncestors($this->menu, $this->wpService);
    }

    public function appendDataFromAncestorIds(): void
    {
        $this->menu = new AppendDataFromAncestorIds($this->menu, $this->wpService);
    }

    public function tryGetPageTreeMenuItemsFromCache(): void
    {
        $this->menu = new TryGetPageTreeMenuItemsFromCache($this->menu);
    }

    public function pageTreeMenuItemsFormatter(): void
    {
        $this->menu = new PageTreeMenuItemsFormatter($this->menu);
    }

    public function pageTreeAppendMenuItemsHref(): void
    {
        $this->menu = new PageTreeAppendMenuItemsHref($this->menu, $this->wpService);
    }

    public function pageTreeAppendMenuItemsCustomTitle(): void
    {
        $this->menu = new PageTreeAppendMenuItemsCustomTitle($this->menu);
    }

    public function pageTreeAppendMenuItemsAncestors(): void
    {
        $this->menu = new PageTreeAppendMenuItemsAncestors($this->menu);
    }

    public function pageTreeAppendMenuItemIsCurrentPage(): void
    {
        $this->menu = new PageTreeAppendMenuItemIsCurrentPage($this->menu);
    }

    public function pageTreeAppendChildren(): void
    {
        $this->menu = new PageTreeAppendChildren($this->menu);
    }

    public function pageTreeSetMenuItemsCache(): void
    {
        $this->menu = new PageTreeSetMenuItemsCache($this->menu);
    }

    public function getMenu(): NewMenuInterface
    {
        return $this->menu;
    }
}