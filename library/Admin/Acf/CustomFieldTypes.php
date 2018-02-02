<?php

namespace Municipio\Admin\Acf;

class CustomFieldTypes
{
    public function __construct()
    {
        add_action('acf/include_field_types',   array($this, 'includeFieldTypes'));
    }

    public function includeFieldTypes()
    {
        new \Municipio\Admin\Acf\Fields\Pricons();
    }
}
