<?php

namespace Municipio\Kulturkortet\Helper;

class ActionCreator
{
    public static function create(string $label, string $url, string $icon): array
    {
        return [
            'label' => $label,
            'href' => $url,
            'style' => 'tiles',
            'icon' => [
                'icon' => $icon,
                'size' => 'md'
            ]
        ];
    }
}