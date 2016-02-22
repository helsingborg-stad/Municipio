<?php

namespace Municipio\Admin\Options;

class Timestamp
{
    public function __construct()
    {
        add_filter('acf/load_field/name=show_date_updated', array($this, 'loadPostTypesToCheckbox'), 10, 3);
        add_filter('acf/load_field/name=show_date_published', array($this, 'loadPostTypesToCheckbox'), 10, 3);
    }

    public function loadPostTypesToCheckbox($field)
    {
        $field['choices'] = array_diff(get_post_types(array(),'names'), array("revision", "acf-field", "acf-field-group", "nav_menu_item"));
        return $field;
    }
}
