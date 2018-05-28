<?php

namespace Municipio\Controller;

class PageTwoColumn extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $this->data['contentGridSize'] = 'grid-md-8 grid-lg-9';
    }
}
