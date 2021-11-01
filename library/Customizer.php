<?php

namespace Municipio;

class Customizer
{
    public const KIRKI_CONFIG = "municipio_config";

    public function __construct()
    {
        //Load embedded kirki
        $this->loadEmbeddedKirki();

        //Kirki failed to load, handle
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
        }, 10);
    }

    /**
     * Load embedded kirki
     *
     * @return void
     */
    public function loadEmbeddedKirki() {
        $kirkiFilePath = rtrim(MUNICIPIO_PATH, '/') . '/vendor/kirki/kirki.php'; 
        if(file_exists($kirkiFilePath)) {
            include_once($kirkiFilePath);
        }
    }

    /**
     * Init customizer toolset
     *
     * @return void
     */
    public function init()
    {
        \Kirki::add_config(self::KIRKI_CONFIG, array(
            'capability'    => 'edit_theme_options',
            'option_type'   => 'theme_mod',
        ));

        //Applicators [Stuff that make effect on the frontend]
        new \Municipio\Customizer\Applicators\Modifiers();
        new \Municipio\Customizer\Applicators\ControllerVariables();
        new \Municipio\Customizer\Applicators\Css();

        //Define panels
        new \Municipio\Customizer\Panels\Design();
        new \Municipio\Customizer\Panels\Example();
    }
}
