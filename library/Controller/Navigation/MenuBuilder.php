<?php

namespace Municipio\Controller\Navigation;

use Municipio\Controller\Navigation\Config\MenuConfigInterface;
use Municipio\Controller\Navigation\Decorators\Accessibility\AppendPrintMenuItem;
use Municipio\Controller\Navigation\Decorators\Accessibility\ApplyAccessibilityItemsDeprecatedFilter;
use Municipio\Controller\Navigation\Decorators\Accessibility\ApplyAccessibilityItemsFilter;
use Municipio\Controller\Navigation\Decorators\Breadcrumb\AppendArchiveMenuItem;
use Municipio\Controller\Navigation\Decorators\Breadcrumb\AppendHomeIconMenuItem;
use Municipio\Controller\Navigation\Decorators\Breadcrumb\AppendPageTreeAncestorsMenuItems;
use Municipio\Controller\Navigation\Decorators\Breadcrumb\ApplyBreadcrumbItemsFilter;
use Municipio\Controller\Navigation\Decorators\Menu\AppendAcfFields;
use Municipio\Controller\Navigation\Decorators\Menu\AppendDataFromAncestorIds;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeAppendMenuItemIsCurrentPage;
use Municipio\Controller\Navigation\Decorators\Menu\AppendMenuItems;
use Municipio\Controller\Navigation\Decorators\Menu\ApplyMenuItemFilter;
use Municipio\Controller\Navigation\Decorators\Menu\ApplyMenuItemsFilter;
use Municipio\Controller\Navigation\Decorators\Menu\ApplyNestedMenuItemsFilter;
use Municipio\Controller\Navigation\Decorators\Menu\ConvertStaticMenuItemsToPageTreeItems;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeGetAncestors;
use Municipio\Controller\Navigation\Decorators\Menu\MapMenuItemsAcfFieldValues;
use Municipio\Controller\Navigation\Decorators\Menu\MapMenuItemsFromObjectToArray;
use Municipio\Controller\Navigation\Decorators\Menu\MapMenuItemsIsAncestor;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeAppendChildren;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeAppendMenuItemsAncestors;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeAppendMenuItemsCustomTitle;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeAppendMenuItemsHref;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeMenuItemsFormatter;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeSetMenuItemsCache;
use Municipio\Controller\Navigation\Decorators\Menu\RemoveSubLevels;
use Municipio\Controller\Navigation\Decorators\Menu\RemoveTopLevel;
use Municipio\Controller\Navigation\Decorators\Menu\StructureMenuItems;
use Municipio\Controller\Navigation\Decorators\Menu\TryGetPageTreeMenuItemsFromCache;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Controller\Navigation\MenuBuilderInterface;

class MenuBuilder implements MenuBuilderInterface
{
    private MenuInterface $menu;
    private MenuConfigInterface $menuConfig;

    public function __construct(MenuConfigInterface $defaultMenuConfig, private $acfService, private $wpService) {
        $this->menuConfig = $defaultMenuConfig;
        $this->initializeMenu();
    }

    private function initializeMenu(): void
    {
        $this->menu = Menu::factory($this->menuConfig);
    }

    public function setConfig(MenuConfigInterface $menuConfig): void
    {
        $this->menuConfig = $menuConfig;
        $this->initializeMenu();
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
        $this->menu = new AppendPageTreeAncestorsMenuItems($this->menu, $this->wpService);
    }

    public function applyBreadcrumbItemsFilter(): void
    {
        $this->menu = new ApplyBreadcrumbItemsFilter($this->menu, $this->wpService);
    }

    public function appendPrintMenuItem(): void
    {
        $this->menu = new AppendPrintMenuItem($this->menu, $this->wpService);
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
        $this->menu = new PageTreeAppendChildren($this->menu, $this->wpService);
    }

    public function pageTreeSetMenuItemsCache(): void
    {
        $this->menu = new PageTreeSetMenuItemsCache($this->menu);
    }

    public function convertStaticMenuItemsToPageTreeItems(): void
    {
        $this->menu = new ConvertStaticMenuItemsToPageTreeItems($this->menu);
    }

    public function appendAcfFields(): void
    {
        $this->menu = new AppendAcfFields($this->menu, $this->wpService, $this->acfService);
    }

    public function getMenu(): MenuInterface
    {
        return $this->menu;
    }
}