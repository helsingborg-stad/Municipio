<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\MenuOrderTransformer;
use Municipio\Controller\Header\AlignmentTransformer;

class Flexible implements HeaderInterface
{
    private bool $isResponsive;
    private bool $hasMegaMenu;
    private bool $hasSearch;
    private MenuOrderTransformer $menuOrderTransformerInstance;
    private AlignmentTransformer $alignmentTransformerInstance;
    private string $headerSettingKey = 'header_sortable_section_';
    private string $headerSettingKeyResponsive = 'Responsive';

    public function __construct(private object $customizer)
    {
        $this->isResponsive                 = !empty($this->customizer->headerEnableResponsiveOrder);
        $this->hasMegaMenu                  = false;
        $this->hasSearch                    = false;
        $this->menuOrderTransformerInstance = new MenuOrderTransformer('@md');
        $this->alignmentTransformerInstance = new AlignmentTransformer($this->getHiddenMenuItemsData());
    }

    private function getHiddenMenuItemsData()
    {
        $hiddenData = !empty($this->customizer->headerSortableHiddenStorage) ?
            $this->customizer->headerSortableHiddenStorage :
            "{}";

        return json_decode($hiddenData);
    }

    public function getHeaderData(): array
    {
        $upperItems = $this->getItems('main_upper');
        $lowerItems = $this->getItems('main_lower');
        [$upperHeader, $lowerHeader] = $this->getHeaderSettings($upperItems, $lowerItems);
        
        return [
            'upperHeader' => $upperHeader,
            'lowerHeader' => $lowerHeader,
            'upperItems'  => $upperItems,
            'lowerItems'  => $lowerItems,
            'hasMegaMenu' => $this->hasMegaMenu,
            'hasSearch'   => $this->hasSearch,
        ];
    }

    private function getHeaderSettings($upperItems, $lowerItems): array
    {
        $upperSettings = [];
        $lowerSettings = [];

        if (!empty($this->customizer->headerSticky)) {
            $upperSettings['sticky'] = empty($lowerItems) ? true : false;
            $upperSettings['sticky'] = true;
        }

        if (!empty($this->customizer->headerBackground)) {
            $upperSettings['backgroundColor'] = empty($lowerItems) ? $this->customizer->headerBackground : 'default';
            $lowerSettings['backgroundColor'] = $this->customizer->headerBackground;
        }


        return [
            array_merge($this->defaultHeaderSettings(), $upperSettings),
            array_merge($this->defaultHeaderSettings(), $lowerSettings)
        ];
    }

    private function defaultHeaderSettings(): array 
    {
        return [
            'sticky' => false,
            'backgroundColor' => 'default',
        ];
    }

    private function getItems(string $section): array
    {
        [$setting, $settingCamelCased]              = $this->getSettingName($section);
        [$desktopOrderedItems, $mobileOrderedItems] = $this->getOrderedMenuItems($settingCamelCased);

        $this->hasMegaMenu = $this->hasMegaMenu || in_array('mega-menu', $desktopOrderedItems);
        $this->hasSearch   = $this->hasSearch || in_array('search-modal', $desktopOrderedItems);

        $items = $this->menuOrderTransformerInstance->transform($desktopOrderedItems, $mobileOrderedItems);
        $items = $this->alignmentTransformerInstance->transform($items, $setting);


        return $items;
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
