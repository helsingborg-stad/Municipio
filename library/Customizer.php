<?php

namespace Municipio;

class Customizer
{
    public const KIRKI_CONFIG = "municipio_config";

    public function __construct()
    {
        add_action('init', function () {
            if (class_exists("Kirki")) {
                return $this->init();
            }

            wp_die(
                __("Kirki Customizer framework is required"),
                __("Plugin install required"),
                [
                    'link_url' => "https://github.com/kirki-framework/kirki.git",
                    'link_text' => __("Install plugin", 'municipio')
                ]
            );
        });
    }

    public function init()
    {
        \Kirki::add_config(self::KIRKI_CONFIG, array(
            'capability'    => 'edit_theme_options',
            'option_type'   => 'theme_mod',
        ));

        //Applicators [Stuff that make effect on the frontend]
        new \Municipio\Customizer\Applicators\Modifiers();

        //Define panels
        new \Municipio\Customizer\Panels\Design();
        new \Municipio\Customizer\Panels\Example();
    }
}
