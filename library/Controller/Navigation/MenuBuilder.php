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
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeAppendMenuItemsFetchUrl;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeAppendMenuItemsHref;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeMenuItemsFormatter;
use Municipio\Controller\Navigation\Decorators\Menu\PageTreeSetMenuItemsCache;
use Municipio\Controller\Navigation\Decorators\Menu\RemoveSubLevels;
use Municipio\Controller\Navigation\Decorators\Menu\RemoveTopLevel;
use Municipio\Controller\Navigation\Decorators\Menu\StandardMenuWithPageTreeSubitemsAppendAncestors;
use Municipio\Controller\Navigation\Decorators\Menu\StandardMenuWithPageTreeSubitemsAppendHasChildren;
use Municipio\Controller\Navigation\Decorators\Menu\StructureMenuItems;
use Municipio\Controller\Navigation\Decorators\Menu\TryGetPageTreeMenuItemsFromCache;
use Municipio\Controller\Navigation\MenuInterface;
use Municipio\Controller\Navigation\MenuBuilderInterface;

/**
 * The builder can construct several types of menus using the same building steps.
 */
class MenuBuilder implements MenuBuilderInterface
{
    private MenuInterface $menu;
    private MenuConfigInterface $menuConfig;

    /**
     * Constructor
     */
    public function __construct(MenuConfigInterface $defaultMenuConfig, private $acfService, private $wpService)
    {
        $this->menuConfig = $defaultMenuConfig;
        $this->initializeMenu();
    }

    /**
     * Initialize menu
     */
    private function initializeMenu(): void
    {
        $this->menu = Menu::factory($this->menuConfig);
    }

    /**
     * Set menu configuration
     *
     * @param MenuConfigInterface $menuConfig
     */
    public function setConfig(MenuConfigInterface $menuConfig): void
    {
        $this->menuConfig = $menuConfig;
        $this->initializeMenu();
    }

    /**
     * Get menu configuration
     *
     * @return MenuConfigInterface
     */
    public function appendMenuItems(): void
    {
        $this->menu = new AppendMenuItems($this->menu);
    }

    /**
     * Map menu items from object to array
     */
    public function mapMenuItemsFromObjectToArray(): void
    {
        $this->menu = new MapMenuItemsFromObjectToArray($this->menu);
    }

    /**
     * Map menu items ACF field values
     */
    public function mapMenuItemsAcfFieldValues(): void
    {
        $this->menu = new MapMenuItemsAcfFieldValues($this->menu, $this->acfService);
    }

    /**
     * Map menu items is ancestor
     */
    public function mapMenuItemsIsAncestor(): void
    {
        $this->menu = new MapMenuItemsIsAncestor($this->menu);
    }

    /**
     * Apply menu item filter
     */
    public function applyMenuItemFilter(): void
    {
        $this->menu = new ApplyMenuItemFilter($this->menu, $this->wpService);
    }

    /**
     * Append home icon menu item
     */
    public function appendHomeIconMenuItem(): void
    {
        $this->menu = new AppendHomeIconMenuItem($this->menu, $this->wpService);
    }

    /**
     * Append archive menu item
     */
    public function appendArchiveMenuItem(): void
    {
        $this->menu = new AppendArchiveMenuItem($this->menu, $this->wpService);
    }

    /**
     * Append page tree ancestors menu items
     */
    public function appendPageTreeAncestorsMenuItems(): void
    {
        $this->menu = new AppendPageTreeAncestorsMenuItems($this->menu, $this->wpService);
    }

    /**
     * Apply breadcrumb items filter
     */
    public function applyBreadcrumbItemsFilter(): void
    {
        $this->menu = new ApplyBreadcrumbItemsFilter($this->menu, $this->wpService);
    }

    /**
     * Append print menu item
     */
    public function appendPrintMenuItem(): void
    {
        $this->menu = new AppendPrintMenuItem($this->menu, $this->wpService);
    }

    /**
     * Apply accessibility items deprecated filter
     */
    public function applyAccessibilityItemsDeprecatedFilter(): void
    {
        $this->menu = new ApplyAccessibilityItemsDeprecatedFilter($this->menu, $this->wpService);
    }

    /**
     * Apply accessibility items filter
     */
    public function applyAccessibilityItemsFilter(): void
    {
        $this->menu = new ApplyAccessibilityItemsFilter($this->menu, $this->wpService);
    }

    /**
     * Apply menu items filter
     */
    public function applyMenuItemsFilter(): void
    {
        $this->menu = new ApplyMenuItemsFilter($this->menu, $this->wpService);
    }

    /**
     * Remove sub levels
     */
    public function removeSubLevels(): void
    {
        $this->menu = new RemoveSubLevels($this->menu);
    }

    /**
     * Remove top level
     */
    public function removeTopLevel(): void
    {
        $this->menu = new RemoveTopLevel($this->menu);
    }

    /**
     * Structure menu items
     */
    public function structureMenuItems(): void
    {
        $this->menu = new StructureMenuItems($this->menu);
    }

    /**
     * Apply nested menu items filter
     */
    public function applyNestedMenuItemsFilter(): void
    {
        $this->menu = new ApplyNestedMenuItemsFilter($this->menu, $this->wpService);
    }

    /**
     * Page tree get ancestors
     */
    public function pageTreeGetAncestors(): void
    {
        $this->menu = new PageTreeGetAncestors($this->menu, $this->wpService);
    }

    /**
     * Append data from ancestor ids
     */
    public function appendDataFromAncestorIds(): void
    {
        $this->menu = new AppendDataFromAncestorIds($this->menu, $this->wpService);
    }

    /**
     * Try get page tree menu items from cache
     */
    public function tryGetPageTreeMenuItemsFromCache(): void
    {
        $this->menu = new TryGetPageTreeMenuItemsFromCache($this->menu);
    }

    /**
     * Page tree menu items formatter
     */
    public function pageTreeMenuItemsFormatter(): void
    {
        $this->menu = new PageTreeMenuItemsFormatter($this->menu);
    }

    /**
     * Page tree append menu items href
     */
    public function pageTreeAppendMenuItemsHref(): void
    {
        $this->menu = new PageTreeAppendMenuItemsHref($this->menu, $this->wpService);
    }

    /**
     * Page tree append menu items custom title
     */
    public function pageTreeAppendMenuItemsCustomTitle(): void
    {
        $this->menu = new PageTreeAppendMenuItemsCustomTitle($this->menu);
    }

    /**
     * Page tree append menu items ancestors
     */
    public function pageTreeAppendMenuItemsAncestors(): void
    {
        $this->menu = new PageTreeAppendMenuItemsAncestors($this->menu);
    }

    /**
     * Page tree append menu items custom title
     */
    public function pageTreeAppendMenuItemIsCurrentPage(): void
    {
        $this->menu = new PageTreeAppendMenuItemIsCurrentPage($this->menu);
    }

    /**
     * Page tree append children
     */
    public function pageTreeAppendChildren(): void
    {
        $this->menu = new PageTreeAppendChildren($this->menu, $this->wpService);
    }

    /**
     * Page tree set menu items cache
     */
    public function pageTreeSetMenuItemsCache(): void
    {
        $this->menu = new PageTreeSetMenuItemsCache($this->menu);
    }

    /**
     * Convert static menu items to page tree items
     */
    public function convertStaticMenuItemsToPageTreeItems(): void
    {
        $this->menu = new ConvertStaticMenuItemsToPageTreeItems($this->menu);
    }

    /**
     * Append ACF fields
     */
    public function appendAcfFields(): void
    {
        $this->menu = new AppendAcfFields($this->menu, $this->wpService, $this->acfService);
    }

    /**
     * Append fetch url to menu items
     */
    public function pageTreeAppendMenuItemsFetchUrl(): void
    {
        $this->menu = new PageTreeAppendMenuItemsFetchUrl($this->menu, $this->wpService);
    }

    /**
     * Standard menu with page tree subitems append has children
     */
    public function standardMenuWithPageTreeSubitemsAppendHasChildren(): void
    {
        $this->menu = new StandardMenuWithPageTreeSubitemsAppendHasChildren($this->menu);
    }

    /**
     * Standard menu with page tree subitems append ancestors
     */
    public function standardMenuWithPageTreeSubitemsAppendAncestors(): void
    {
        $this->menu = new StandardMenuWithPageTreeSubitemsAppendAncestors($this->menu, $this->wpService);
    }

    /**
     * Get menu
     *
     * @return MenuInterface
     */
    public function getMenu(): MenuInterface
    {
        return $this->menu;
    }
}
