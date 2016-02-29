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

    /**
     * Creates a lead paragraph
     * @param  string $text Text
     * @return string       Markup
     */
    public function theLead($text)
    {
        return '<p class="lead">' . $text . '</p>';
    }

    /**
     * Color scheme body class
     * @param  array $classes Default classes
     * @return array          Modified classes
     */
    public function colorScheme($classes)
    {
        $color = get_field('color_scheme', 'option');

        if (!$color) {
            return $classes;
        }

        $classes[] = 'theme-' . $color;
        return $classes;
    }

    /**
     * is-child-theme body class
     * @param  array  $classes Default classes
     * @return array           Modified calsses
     */
    public function isChildTheme($classes)
    {
        if (is_child_theme()) {
            $classes[] = "is-child-theme";
        }

        return $classes;
    }
}
