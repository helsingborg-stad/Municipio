<?php

namespace Municipio\Controller\Header\Helper;

class ExtractMenuItems
{
    private array $extractedItems = [];

    public function __construct(private object $customizer)
    {}

    public function extract(): array
    {

        $jsonString = $this->customizer?->headerSortableHiddenStorage ?? '{}';
        $decodedJsonString = json_decode($jsonString, true);

        $this->extractedItems = $decodedJsonString;

        return $this->extractedItems;
    }

    public function getHeaderItems(string $id): array
    {
        $data = $this->extract();
        $key = $this->getKey($id);
        $responsiveKey = $this->getKey($id, true);
        
        echo '<pre>' . print_r( $data, true ) . '</pre>';die;
        return $this->extractedItems;
    }

    private function getKey(string $id, bool $responsive = false): string
    {
        return 'header_sortable_section_main_' . $id . ($responsive ? '_responsive' : '');
    }
}