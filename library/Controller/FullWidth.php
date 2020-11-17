<?php

namespace Municipio\Controller;

class FullWidth extends \Municipio\Controller\BaseController
{
    public function init()
    {
        $this->data['contentGridSize'] = 'o-grid-12@md';

        // Show or hide sidebars
        $this->data['showSidebars'] = false;
    }
}
