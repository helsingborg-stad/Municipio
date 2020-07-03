<?php

namespace Municipio\Helper;

class Notice
{
    /**
     * Add a notice
     * @param string $text  Notice text
     * @param string $class Notice html class
     */
    public static function add($text, $class = 'warning', $icon = null, $buttons = null)
    {
        add_filter('Municipio/viewData', function ($data) use ($text, $class, $icon, $buttons) {
            $data['notice'][] = [
                'type' => $class,
                'message' => [
                    'text' => $text,
                    'size' => 'sm'
                ],
                'icon' => [
                    'name' => $icon,
                    'size' => 'md',
                    'color' => 'white'
                ]
            ]; 
            return $data;
        });
    }
}
