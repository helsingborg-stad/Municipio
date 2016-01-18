<?php

namespace Municipio\Helper;

class Notice
{
    public function add($text, $class = 'warning')
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
