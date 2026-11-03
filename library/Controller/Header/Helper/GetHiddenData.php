<?php

namespace Municipio\Controller\Header\Helper;

class GetHiddenData
{
    private object $data;
    
    public function __construct(
        private object $customizer
    )
    {
    }
    
    public function get(): object
    {
        if (!empty($this->data)) {
            return $this->data;
        }

        return $this->getHiddenMenuItemsData();
    }

    // Handles the hidden menu data in the customizer.
    private function getHiddenMenuItemsData(): object
    {
        $hiddenData = !empty($this->customizer->headerSortableHiddenStorage) ? $this->customizer->headerSortableHiddenStorage : '{}';

        if (is_array($hiddenData)) {
            $hiddenData = wp_json_encode($hiddenData);
        }

        if (is_object($hiddenData)) {
            return $hiddenData;
        }

        $decodedValue = json_decode((string) $hiddenData);

        return is_object($decodedValue) ? $decodedValue : (object) [];
    }
}