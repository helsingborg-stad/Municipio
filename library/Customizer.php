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

            if(is_admin()) {
                add_action('admin_notices', function () {
                    echo '<div class="notice notice-error"><p>To run <strong>Municpio</strong> theme, please install & activate <a href="https://kirki.org/">Kirki Customizer Framework</a>.</p></div>';
                });
                return; 
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

        new \Municipio\Customizer\Panels\Design();
    }
}
