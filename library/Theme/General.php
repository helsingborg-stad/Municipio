<?php

namespace Municipio\Theme;

class General
{
    public function __construct()
    {
        add_filter('body_class', array($this, 'colorScheme'));
    }

    public function colorScheme($classes)
    {
        $color = get_field('color_scheme', 'option');

        if (!$color) {
            return $classes;
        }

        $classes[] = 'theme-' . $color;
        return $classes;
    }
}
