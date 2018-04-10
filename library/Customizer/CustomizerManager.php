<?php

namespace Municipio\Customizer;

class CustomizerManager
{
    public $config = 'municipio_config';

    public function __construct()
    {
        $this->kirkiConfig();

        new \Municipio\Customizer\Header\CustomizerHeader($this);
    }

    /**
     * Create configuration that can be inherited by customizer fields
     * @param  array $config Kirki config
     * @return array
     */
    public function kirkiConfig()
    {
        add_filter('kirki/config', array($this, 'krikiPath'));

        \Kirki::add_config($this->config, array(
            'capability'    => 'edit_theme_options',
            'option_type'   => 'theme_mod',
        ));
    }

    /**
     * Make sure child theme get correct kirkiPath
     * @param  array $config Kirki config
     * @return array
     */
    public function krikiPath($config)
    {
        if (!is_array($config)) {
            $config = array();
        }

        $config['url_path'] = get_template_directory_uri() . '/vendor/aristath/kirki/';

        return $config;
    }
}
