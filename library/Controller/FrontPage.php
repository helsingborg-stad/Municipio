<?php

namespace Municipio\Controller;

class FrontPage extends \Municipio\Controller\BaseController
{
    public function init()
    {

    }

    public function registerTemplate()
    {
        \Municipio\Helper\Template::add('Front page', 'front-page.blade.php');
    }
}
