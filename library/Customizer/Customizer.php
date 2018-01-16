<?php

namespace Municipio\Customizer;

class Customizer
{
    public function __construct()
    {
        $this->config();
        $this->init();
    }

    public function config()
    {
        \Kirki::add_config( 'municipio_config', array(
            'capability'    => 'edit_theme_options',
            'option_type'   => 'theme_mod',
        ));
    }

    public function init()
    {
        new \Municipio\Customizer\Header\HeaderPanel();
    }
}
