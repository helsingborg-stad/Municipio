<?php

namespace Municipio\Controller;

/**
 * Class Single
 * @package Municipio\Controller
 */
class Page extends \Municipio\Controller\Singular
{
    public function init()
    {
        parent::init();
        $this->data['showSidebars'] = true;
    }
}
