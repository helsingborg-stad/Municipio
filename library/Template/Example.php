<?php

namespace Municipio\Template;

class Example
{
    public function __construct()
    {
        \Municipio\Helper\Template::add('Example', 'templates/page/example-page.blade.php');
    }
}
