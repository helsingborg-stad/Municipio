<?php

namespace Municipio\Customizer\Header;

class Visibility
{
    public function __construct()
    {
        add_filter('Municipio/Customizer/Classes', array($this, 'visibilityClasses'), 6, 2);
    }

    public function visibilityClasses($classes, $id)
    {
        if (is_array(get_theme_mod($id . '-header-visibility')) && !empty(get_theme_mod($id . '-header-visibility'))) {
            $classes = array_merge($classes, get_theme_mod($id . '-header-visibility'));
        }

        return $classes;
    }
}
