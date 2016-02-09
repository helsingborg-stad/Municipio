<?php

namespace Municipio\Theme;

class General
{
    public function __construct()
    {
        add_filter('body_class', array($this, 'colorScheme'));
        add_filter('body_class', array($this, 'isChildTheme'));

        add_filter('the_lead', array($this, 'theLead'));
    }

    public function theLead($text)
    {
        return '<p class="lead">' . $text . '</p>';
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

    public function isChildTheme($classes)
    {
        if (is_child_theme()) {
            $classes[] = "is-child-theme";
        }

        return $classes;
    }
}
