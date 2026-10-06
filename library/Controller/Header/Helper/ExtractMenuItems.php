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

        $items = [
            Enums::HEADER_BREAKPOINT::DESKTOP->value => $this->customizer->$key ?? [],
            Enums::HEADER_BREAKPOINT::MOBILE->value => $this->customizer->$responsiveKey ?? [],
        ];

        $items = $this->addMenuItemDataToItems($items, $id);

        return $items;
    }

    private function addMenuItemDataToItems(array $items, string $id): array
    {
        $data = $this->extract();
        foreach ($items as $breakpoint => $breakpointItems) {
            $key = $this->getMenuItemDataKey(Enums::HEADER_KEY::from($id), $breakpoint === Enums::HEADER_BREAKPOINT::MOBILE->value);

        }

        return $items;
    }

    private function getMenuItemDataKey(HeaderKey $id, bool $responsive = false): string
    {
        return 'header_sortable_section_main_' . $id->value . ($responsive ? '_responsive' : '');
    }


    private function getKey(HeaderKey $id, bool $responsive = false): string
    {
        return 'headerSortableSectionMain' . ucfirst($id->value) . ($responsive ? 'Responsive' : '');
    }
}