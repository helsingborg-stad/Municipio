<?php

namespace Intranet\Theme;

class Header
{
    public function __construct()
    {
        add_filter('acf/load_field/name=header_layout', array($this, 'addIntranetHeader'));
    }

    public function addIntranetHeader($field)
    {
        $field['choices']['intranet'] = 'Intranet';
        return $field;
    }
}
