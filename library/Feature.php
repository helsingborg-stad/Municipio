<?php

namespace Municipio;

class Feature
{
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'customizerHeader'), 5);
    }

    public function customizerHeader()
    {
        add_filter('Municipio/Controller/BaseController/Customizer', array($this, 'toogleFeatures'));
        add_filter('Municipio/Theme/Enqueue/Bem', array($this, 'toogleFeatures'));
        add_filter('Municipio/Widget/Widgets/CustomizerWidgets', array($this, 'toogleFeatures'));
    }

    public function toogleFeatures($boolean)
    {
        if (function_exists('get_field') && get_field('theme_mode', 'options') >= 2) {
            return true;
        }

        return false;
    }
}
