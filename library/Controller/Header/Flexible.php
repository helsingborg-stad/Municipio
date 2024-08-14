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
        return [
            'upper'       => $this->getItems('main_upper'),
            'lower'       => $this->getItems('main_lower'),
            'hasMegaMenu' => $this->hasMegaMenu,
            'hasSearch'   => $this->hasSearch,
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
            ($this->isResponsive && isset($this->customizer->{$settingCamelCased . 'Responsive'}))
                ? $this->customizer->{$settingCamelCased . 'Responsive'}
                : [],
        ];
    }

    private function getSettingName(string $section): array
    {
        $setting = 'header_sortable_section_' . $section;
        return [
        $setting,
        \Municipio\Helper\FormatObject::camelCaseString($setting),
        ];
    }
}
