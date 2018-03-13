<?php

namespace Municipio\Customizer;

class Customizer
{
    public function __construct()
    {
        add_filter('kirki/config', array($this, 'kirkiConfig'));

        $this->config();
        $this->init();
    }

    public function kirkiConfig($config)
    {
        $config['url_path'] = get_template_directory_uri() . '/vendor/aristath/kirki/';
        return $config;
    }

    public function config()
    {
        \Kirki::add_config('municipio_config', array(
            'capability'    => 'edit_theme_options',
            'option_type'   => 'theme_mod',
        ));
    }

    public function init()
    {
        new \Municipio\Customizer\Header();
        new \Municipio\Customizer\Footer();
    }
}
