<?php

namespace Municipio\Theme;

class CustomTemplates
{
    public function __construct()
    {
        \Municipio\Helper\Template::add(
            __('Full width', 'municipio'),
            \Municipio\Helper\Template::locateTemplate('full-width.blade.php')
        );
    }
}
