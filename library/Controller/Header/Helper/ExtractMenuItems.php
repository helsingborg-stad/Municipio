<?php

namespace Municipio\Controller\Header\Helper;

class ExtractMenuItems
{
    private array $extractedItems = [];
    private array $extractedRawData = [];

    public function __construct(private object $customizer)
    {}

    public function extract(): array
    {
        if (!empty($this->extractedRawData)) {
            return $this->extractedRawData;
        }

        $jsonString = $this->customizer?->headerSortableHiddenStorage ?? '{}';

        $decodedJsonString = json_decode($jsonString, true);

        $this->extractedRawData = $decodedJsonString;

        return $this->extractedRawData;
    }

    public function getAllHeaders(): array
    {
        $this->extract();

        foreach (Enums::HEADER_KEY::cases() as $value) {
            $this->extractedItems[$value->value] = $this->getHeaderItems($value->value);
        }

        return $this->extractedItems;
    }

    public function getHeaderItems(string $id): array
    {
        
        if (isset($this->extractedItems[$id])) {
            return $this->extractedItems[$id];
        }
            
        $key = $this->getKey(Enums::HEADER_KEY::from($id));
        $responsiveKey = $this->getKey(Enums::HEADER_KEY::from($id), true);

        $desktopValue = $this->customizer->$key ?? [];
        $mobileValue = $this->customizer->$responsiveKey ?? [];

        $items = [
            Enums::HEADER_BREAKPOINT::DESKTOP->value => is_string($desktopValue)
                ? (json_decode($desktopValue, true) ?: [])
                : (is_array($desktopValue) ? $desktopValue : []),
            Enums::HEADER_BREAKPOINT::MOBILE->value => is_string($mobileValue)
                ? (json_decode($mobileValue, true) ?: [])
                : (is_array($mobileValue) ? $mobileValue : []),
        ];

        $items = $this->addMenuItemDataToItems($items, $id);

        return $items;
    }

    private function addMenuItemDataToItems(array $items, string $id): array
    {
        $data = $this->extract();
        $mappedItems = [];
        foreach ($items as $breakpoint => $breakpointItems) {
            $key = $this->getMenuItemDataKey(Enums::HEADER_KEY::from($id), $breakpoint === Enums::HEADER_BREAKPOINT::MOBILE->value);

            foreach ($breakpointItems as $item) {
                if (isset($data[$key][$item])) {
                    $mappedItems[$breakpoint][$item] = array_merge($this->getDefaultValues(), $data[$key][$item]);
                }
            }
        }

        return $mappedItems;
    }

    private function getMenuItemDataKey(HeaderKey $id, bool $responsive = false): string
    {
        return 'header_sortable_section_main_' . $id->value . ($responsive ? '_responsive' : '');
    }

    private function getDefaultValues(): array
    {
        return [
            'align' => 'right',
            'margin' => 'none',
            'buttonStyle' => 'basic',
            'buttonSize' => 'md',
            'buttonColor' => 'inherit',
        ];
    }


    private function getKey(HeaderKey $id, bool $responsive = false): string
    {
        return 'headerSortableSectionMain' . ucfirst($id->value) . ($responsive ? 'Responsive' : '');
    }
}