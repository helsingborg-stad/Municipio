<?php

namespace Municipio\Theme;

class CustomTemplates
{
    public function __construct()
    {
        \Municipio\Helper\Template::add(
            __('One Page', 'municipio'),
            \Municipio\Helper\Template::locateTemplate('one-page.blade.php'),
            'all'
        );
    }
}
