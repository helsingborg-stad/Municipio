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
        $postTypes = array();
        foreach (get_post_types() as $key => $postType) {
            $args = get_post_type_object($postType);

            if (!$args->public) {
                continue;
            }

            $postTypes[$postType] = $args->label;
        }

        $field['choices'] = $postTypes;
        return $field;
    }
}
