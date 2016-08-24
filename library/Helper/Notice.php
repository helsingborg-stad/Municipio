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
        add_filter('HbgBlade/data', function ($data) use ($text, $class, $icon, $buttons) {
            $data['notice'] = array(
                'class' => $class,
                'icon' => $icon,
                'text' => $text,
                'buttons' => $buttons
            );

            return $data;
        });
    }
}
