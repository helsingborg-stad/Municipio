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

        $data = $this->extract();
        $key = $this->getKey(Enums::HEADER_KEY::from($id));
        $responsiveKey = $this->getKey(Enums::HEADER_KEY::from($id), true);

        $items = [
            Enums::HEADER_BREAKPOINT::DESKTOP->value => $data[$key] ?? [],
            Enums::HEADER_BREAKPOINT::MOBILE->value => $data[$responsiveKey] ?? [],
        ];

        return $items;
    }

    private function getKey(HeaderKey $id, bool $responsive = false): string
    {
        return 'header_sortable_section_main_' . $id->value . ($responsive ? '_responsive' : '');
    }
}