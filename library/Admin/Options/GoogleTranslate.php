<?php

namespace Municipio\Admin\Options;

class GoogleTranslate
{
    public function __construct()
    {
        add_filter('acf/load_field/name=google_translate_menu', array($this, 'loadNavMenus'), 10, 3);
    }

    public function loadNavMenus($field)
    {
        $field['choices'] = get_registered_nav_menus();
        return $field;
    }
}
