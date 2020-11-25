<?php

namespace Municipio\Controller;

class FullWidth extends \Municipio\Controller\Singular
{
    public function init()
    {
        $this->data = parent::init();

        $this->data['contentGridSize'] = 'o-grid-12@md';

        // Show or hide sidebars
        $this->data['showSidebars'] = false;
    }
}
