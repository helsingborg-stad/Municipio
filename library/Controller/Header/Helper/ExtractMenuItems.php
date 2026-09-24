<?php

namespace Municipio\Controller\Header\Helper;

class ExtractMenuItems
{
    public function __construct(private object $customizer)
    {}

    public function get(): array
    {
        $json = $this->customizer?->headerSortableHiddenStorage ?? '{}';
        echo '<pre>' . print_r( $json, true ) . '</pre>';die;
        return [];
    }
}