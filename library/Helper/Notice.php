<?php

namespace Municipio\Helper;

class Notice
{
    /**
     * Add a notice
     * @param string $text  Notice text
     * @param string $class Notice html class
     */
    public static function add($text, $class = 'warning')
    {
        add_filter('HbgBlade/data', function ($data) use ($text, $class) {
            $data['notice'] = array(
                'text' => $text,
                'class' => $class
            );

            return $data;
        });
    }
}
