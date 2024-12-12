<?php

namespace Municipio\Helper;

class Notice
{
    /**
     * Add a notice
     * @param string $text  Notice text
     * @param string $class Notice html class
     */
    public static function add($text, $class = 'warning', $icon = null)
    {
        add_filter('Municipio/viewData', function ($data) use ($text, $class, $icon) {
            $data['notice'][] = [
                'type'    => $class,
                'message' => [
                    'text' => $text,
                    'size' => 'sm'
                ],
                'icon'    => [
                    'name'  => $icon,
                    'size'  => 'md',
                    'color' => 'white'
                ],
                'classList' => [
                    't-toast__notice'
                ]
            ];
            return $data;
        });
    }
}
