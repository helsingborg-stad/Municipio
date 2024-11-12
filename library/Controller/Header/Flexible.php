<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\MenuOrderTransformer;
use Municipio\Controller\Header\MenuVisibilityTransformer;
use Municipio\Controller\Header\AlignmentTransformer;
use Municipio\Controller\Header\FlipKeyValueTransformer;
use Municipio\Controller\Header\HeaderVisibilityClasses;

class Flexible implements HeaderInterface
{
    private bool $isResponsive;
    private bool $hasSearch;
    private bool $nonStickyMegaMenu;
    private MenuOrderTransformer $menuOrderTransformerInstance;
    private AlignmentTransformer $alignmentTransformerInstance;
    private FlipKeyValueTransformer $flipKeyValueTransformer;
    private MenuVisibilityTransformer $menuVisibilityTransformerInstance;
    private HeaderVisibilityClasses $headerVisibilityClassesInstance;
    private IsResponsiveMenuTransformer $isResponsiveMenu;
    private string $headerSettingKey           = 'header_sortable_section_';
    private string $headerSettingKeyResponsive = 'Responsive';
    private bool $hasSeparateBrandText         = false;

    public function __construct(private object $customizer)
    {
        $this->isResponsive = !empty($this->customizer->headerEnableResponsiveOrder);
        $this->hasSearch    = false;

        $this->headerVisibilityClassesInstance   = new HeaderVisibilityClasses();
        $this->flipKeyValueTransformer           = new FlipKeyValueTransformer();
        $this->isResponsiveMenu                  = new IsResponsiveMenuTransformer();
        $this->menuVisibilityTransformerInstance = new MenuVisibilityTransformer();
        $this->menuOrderTransformerInstance      = new MenuOrderTransformer('@md');
        $this->alignmentTransformerInstance      = new AlignmentTransformer($this->getHiddenMenuItemsData());
    }

    // Gets the header data accessible in the view.
    public function getHeaderData(): array
    {
        $upperItems = $this->getItems('main_upper');
        $lowerItems = $this->getItems('main_lower');

        [$upperHeader, $lowerHeader] = $this->getHeaderSettings($upperItems, $lowerItems);
        
        return [
            'upperHeader'          => $upperHeader,
            'lowerHeader'          => $lowerHeader,
            'upperItems'           => $upperItems['modified'],
            'lowerItems'           => $lowerItems['modified'],
            'hasSearch'            => $this->hasSearch,
            'hasSeparateBrandText' => $this->hasSeparateBrandText,
            'nonStickyMegaMenu'    => $this->nonStickyMegaMenu,
        ];
    }

    // Handles the hidden menu data in the customizer.
    private function getHiddenMenuItemsData()
    {
        $hiddenData = !empty($this->customizer->headerSortableHiddenStorage) ?
            $this->customizer->headerSortableHiddenStorage :
            "{}";

        return json_decode($hiddenData);
    }

    // Gets the header settings.
    private function getHeaderSettings($upperItems, $lowerItems): array
    {
        $upperHeader = [];
        $lowerHeader = [];

        if (!empty($this->customizer->headerSticky)) {
            $upperHeader['sticky'] = empty($lowerItems['modified']) ? true : false;
            $lowerHeader['sticky'] = empty($upperHeader['sticky']);
        }

        $lowerHeaderHasMegaMenu = $this->hasMegaMenu($lowerItems);
        $upperHeaderHasMegaMenu = $this->hasMegaMenu($upperItems);

        $lowerHeader['innerMegaMenu'] = $lowerHeaderHasMegaMenu && !empty($lowerHeader['sticky']);
        $upperHeader['innerMegaMenu'] = $upperHeaderHasMegaMenu && !empty($upperHeader['sticky']);

        $this->nonStickyMegaMenu = ($upperHeaderHasMegaMenu || $lowerHeaderHasMegaMenu) && empty($lowerHeader['innerMegaMenu']) && empty($upperHeader['innerMegaMenu']);

        if (!empty($this->customizer->headerBackground)) {
            $upperHeader['backgroundColor'] = empty($lowerItems['modified']) ? $this->customizer->headerBackground : 'default';
            $lowerHeader['backgroundColor'] = $this->customizer->headerBackground;
        }

        $upperHeader['classList']   = $this->headerVisibilityClassesInstance->getHeaderClasses($upperItems);
        $lowerHeader['classList']   = $this->headerVisibilityClassesInstance->getHeaderClasses($lowerItems);
        $upperHeader['classList'][] = !empty($upperItems['modified']['center']) ? 'c-header--flexible-has-centered-content' : '';
        $lowerHeader['classList'][] = !empty($lowerItems['modified']['center']) ? 'c-header--flexible-has-centered-content' : '';

        return [
            array_merge($this->defaultHeaderSettings(), $upperHeader),
            array_merge($this->defaultHeaderSettings(), $lowerHeader)
        ];
    }

    // Default settings.
    private function defaultHeaderSettings(): array
    {
        return [
            'sticky'          => false,
            'backgroundColor' => 'default',
            'classList'       => []
        ];
    }

    // Handles and returns the modified menu items.
    private function getItems(string $section): array
    {
        // Getting the items
        [$setting, $settingCamelCased]              = $this->getSettingName($section);
        [$desktopOrderedItems, $mobileOrderedItems] = $this->getOrderedMenuItems($settingCamelCased);

        $this->hasSearch            = $this->hasSearch($desktopOrderedItems, $mobileOrderedItems);
        $this->hasSeparateBrandText = $this->hasSeparateBrandText($desktopOrderedItems, $mobileOrderedItems);

        // Building the items
        $items = $this->flipKeyValueTransformer->transform($desktopOrderedItems, $mobileOrderedItems);
        $items = $this->isResponsiveMenu->transform($items, $this->isResponsive);
        $items = $this->menuOrderTransformerInstance->transform($items);
        $items = $this->menuVisibilityTransformerInstance->transform($items);
        $items = $this->alignmentTransformerInstance->transform($items, $setting);

        return $items;
    }

    // Checks if the search is present in the menu.
    private function hasSearch($desktopOrderedItems, $mobileOrderedItems): bool
    {
        return $this->hasSearch || in_array('search-modal', $desktopOrderedItems ?: []) || in_array('search-modal', $mobileOrderedItems ?: []);
    }

    // Checks if the mega menu is present in the menu.
    private function hasMegaMenu(array $items): bool
    {
        return isset($items['desktop']['mega-menu']) || isset($items['mobile']['mega-menu']);
    }

    // Checks if the brand text is separated from logotype.
    private function hasSeparateBrandText(array $desktopOrderedItems, array $mobileOrderedItems)
    {
        return
            $this->hasSeparateBrandText ||
            in_array('brand-text', $desktopOrderedItems) ||
            in_array('brand-text', $mobileOrderedItems);
    }

    // Gets the ordered menu items from the customizer.
    private function getOrderedMenuItems(string $settingCamelCased): array
    {
        return [
            $this->customizer->{$settingCamelCased} ?: [],
            ($this->isResponsive && isset($this->customizer->{$settingCamelCased . $this->headerSettingKeyResponsive}))
                ? $this->customizer->{$settingCamelCased . $this->headerSettingKeyResponsive}
                : [],
        ];
    }

    // Gets the camelCased setting name.
    private function getSettingName(string $section): array
    {
        $setting = $this->headerSettingKey . $section;
        return [
            $setting,
            \Municipio\Helper\FormatObject::camelCaseString($setting),
        ];
    }
}
