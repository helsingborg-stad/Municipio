<?php

namespace Municipio\Helper;

class Notice
{
    /**
     * Add a notice
     * @param string $text  Notice text
     * @param string $class Notice html class
     */
    public static function add($text, $class = 'warning', $icon = null, $action = null, $dismissable = false, $location = 'toast')
    {
        add_filter('Municipio/viewData', function ($data) use ($text, $class, $icon, $action, $dismissable, $location) {
            $data['notice'][$location][] = [
                'type'        => $class,
                'message'     => [
                    'text' => $text,
                    'size' => 'sm'
                ],
                'icon'        => [
                    'name'  => $icon,
                    'size'  => 'md',
                    'color' => 'white'
                ],
                'action'      => $action ?? null,
                'dismissable' => $dismissable ?? false,
                'classList'   => [
                    't-toast__notice'
                ]
            ];
            return $data;
        });
    }
}
