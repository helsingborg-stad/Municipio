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
    private bool $hasMegaMenu;
    private bool $hasSearch;
    private MenuOrderTransformer $menuOrderTransformerInstance;
    private AlignmentTransformer $alignmentTransformerInstance;
    private FlipKeyValueTransformer $flipKeyValueTransformer;
    private MenuVisibilityTransformer $menuVisibilityTransformerInstance;
    private HeaderVisibilityClasses $headerVisibilityClassesInstance;
    private string $headerSettingKey           = 'header_sortable_section_';
    private string $headerSettingKeyResponsive = 'Responsive';

    public function __construct(private object $customizer)
    {
        $this->isResponsive = !empty($this->customizer->headerEnableResponsiveOrder);
        $this->hasMegaMenu  = false;
        $this->hasSearch    = false;

        $this->headerVisibilityClassesInstance   = new HeaderVisibilityClasses();
        $this->flipKeyValueTransformer           = new FlipKeyValueTransformer();
        $this->menuVisibilityTransformerInstance = new MenuVisibilityTransformer();
        $this->menuOrderTransformerInstance      = new MenuOrderTransformer('@md');
        $this->alignmentTransformerInstance      = new AlignmentTransformer($this->getHiddenMenuItemsData());
    }

    public function getHeaderData(): array
    {
        $upperItems                  = $this->getItems('main_upper');
        $lowerItems                  = $this->getItems('main_lower');
        [$upperHeader, $lowerHeader] = $this->getHeaderSettings($upperItems, $lowerItems);

        return [
            'upperHeader' => $upperHeader,
            'lowerHeader' => $lowerHeader,
            'upperItems'  => $upperItems['modified'],
            'lowerItems'  => $lowerItems['modified'],
            'hasMegaMenu' => $this->hasMegaMenu,
            'hasSearch'   => $this->hasSearch,
        ];
    }

    private function getHiddenMenuItemsData()
    {
        $hiddenData = !empty($this->customizer->headerSortableHiddenStorage) ?
            $this->customizer->headerSortableHiddenStorage :
            "{}";

        return json_decode($hiddenData);
    }

    private function getHeaderSettings($upperItems, $lowerItems): array
    {
        $upperHeader = [];
        $lowerHeader = [];

        if (!empty($this->customizer->headerSticky)) {
            $upperHeader['sticky'] = empty($lowerItems['modified']) ? true : false;
            $upperHeader['sticky'] = true;
        }

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

    private function defaultHeaderSettings(): array
    {
        return [
            'sticky'          => false,
            'backgroundColor' => 'default',
        ];
    }

    private function getItems(string $section): array
    {
        // Getting the items
        [$setting, $settingCamelCased]              = $this->getSettingName($section);
        [$desktopOrderedItems, $mobileOrderedItems] = $this->getOrderedMenuItems($settingCamelCased);

        $this->hasMegaMenu = $this->hasMegaMenu($desktopOrderedItems, $mobileOrderedItems);
        $this->hasSearch   = $this->hasSearch($desktopOrderedItems, $mobileOrderedItems);

        // Building the items
        $items = $this->flipKeyValueTransformer->transform($desktopOrderedItems, $mobileOrderedItems);
        $items = $this->menuOrderTransformerInstance->transform($items);
        $items = $this->menuVisibilityTransformerInstance->transform($items);
        $items = $this->alignmentTransformerInstance->transform($items, $setting);

        return $items;
    }

    private function hasSearch($desktopOrderedItems, $mobileOrderedItems): bool
    {
        return $this->hasSearch || in_array('search-modal', $desktopOrderedItems) || in_array('search-modal', $mobileOrderedItems);
    }

    private function hasMegaMenu($desktopOrderedItems, $mobileOrderedItems): bool
    {
        return $this->hasMegaMenu || in_array('mega-menu', $desktopOrderedItems) || in_array('mega-menu', $mobileOrderedItems);
    }

    private function getOrderedMenuItems(string $settingCamelCased): array
    {
        return [
            $this->customizer->{$settingCamelCased} ?? [],
            ($this->isResponsive && isset($this->customizer->{$settingCamelCased . $this->headerSettingKeyResponsive}))
                ? $this->customizer->{$settingCamelCased . $this->headerSettingKeyResponsive}
                : [],
        ];
    }

    private function getSettingName(string $section): array
    {
        $setting = $this->headerSettingKey . $section;
        return [
            $setting,
            \Municipio\Helper\FormatObject::camelCaseString($setting),
        ];
    }
}
