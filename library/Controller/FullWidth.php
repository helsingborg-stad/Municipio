<?php

namespace Municipio\Controller;

/**
 * Class FullWidth
 *
 * @package Municipio\Controller
 */
class FullWidth extends \Municipio\Controller\Singular
{
    /**
     * @return array|void
     */
    public function init()
    {
        parent::init();

        $this->data['contentGridSize'] = 'o-grid-12@md';

        // Show or hide sidebars
        $this->data['showSidebars'] = false;
    }
}
