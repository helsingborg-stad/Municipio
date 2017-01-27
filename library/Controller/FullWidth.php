<?php

namespace Municipio\Controller;

class FullWidth extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $this->data['contentGridSize'] = 'grid-md-12';
    }
}
