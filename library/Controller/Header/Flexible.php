<?php

namespace Municipio\Controller\Header;

use Municipio\Controller\Header\MenuOrderTransformer;
use Municipio\Controller\Header\AlignmentTransformer;

class Flexible implements HeaderInterface
{
    private bool $isResponsive;
    private MenuOrderTransformer $menuOrderTransformerInstance;
    private AlignmentTransformer $alignmentTransformerInstance;

    public function __construct(private object $customizer)
    {
        $this->isResponsive                 = !empty($this->customizer->headerEnableResponsiveOrder);
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
            'upper' => $this->getItems('main_upper'),
            'lower' => $this->getItems('main_lower'),
        ];
    }

    private function getItems(string $section): array
    {
        $setting             = 'header_sortable_section_' . $section;
        $settingCamelCased   = \Municipio\Helper\FormatObject::camelCaseString($setting);
        $desktopOrderedItems = $this->customizer->{$settingCamelCased} ?? [];
        $mobileOrderedItems  = ($this->isResponsive && isset($this->customizer->{$settingCamelCased . 'Responsive'}))
            ? $this->customizer->{$settingCamelCased . 'Responsive'}
            : [];

        $items = $this->menuOrderTransformerInstance->transform($desktopOrderedItems, $mobileOrderedItems);
        $items = $this->alignmentTransformerInstance->decorate($items, $setting);

        return $items;
    }
}
