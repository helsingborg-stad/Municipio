<?php

namespace Municipio\Admin\Options;

class Theme
{
    public function __construct()
    {
        add_filter('acf/load_value/name=show_date_updated', array($this,'loadPostTypesToCheckbox'), 10, 3);
        add_filter('acf/load_value/name=show_date_published', array($this,'loadPostTypesToCheckbox'), 10, 3);
    }

    public function loadPostTypesToCheckbox($value, $post_id, $field)
    {
        return get_post_types();
    }
}
