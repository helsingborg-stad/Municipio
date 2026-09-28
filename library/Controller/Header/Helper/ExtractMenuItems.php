<?php

namespace Municipio\Controller\Header\Helper;

class ExtractMenuItems
{
    private array $extractedItems = [];

    public function __construct(private object $customizer)
    {}

    private function extract(): array
    {
        $jsonString = $this->customizer?->headerSortableHiddenStorage ?? '{}';

        $decodedJsonString = json_decode($jsonString, true);

        $this->extractedItems = $decodedJsonString;

        return $this->extractedItems;
    }

    public function getHeaderItems(string $id): array
    {
        if (isset($this->extractedItems[$id])) {
            return $this->extractedItems[$id];
        }

        $data = $this->extract();
        $key = $this->getKey($id);
        $responsiveKey = $this->getKey($id, true);

        $items = [
            'desktop' => $data[$key] ?? [],
            'mobile' => $data[$responsiveKey] ?? [],
        ];

        return $items;
    }

    private function getKey(string $id, bool $responsive = false): string
    {
        return 'header_sortable_section_main_' . $id . ($responsive ? '_responsive' : '');
    }
}