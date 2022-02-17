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

        /**
         * Fixes issue when using :root selector in
         * output args for Gutenberg editor.
         *
         * Issue adressed here: https://github.com/kirki-framework/kirki/issues/2461.
         * When resolved, this can be removed.
         */
        add_filter('kirki_municipio_config_dynamic_css', function ($styles) {
            $isEditor = (isset($_GET['editor']) && $_GET['editor'] == '1');
            $isStyles = (isset($_GET['action']) && $_GET['action'] == 'kirki-styles');

            if ($isEditor && $isStyles) {
                return str_replace(
                    '.editor-styles-wrapper :root',
                    '.editor-styles-wrapper',
                    $styles
                );
            }
            return $styles;
        }, 20);
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
        if (!defined("WEB_FONT_DISABLE_INLINE"))
            define("WEB_FONT_DISABLE_INLINE", true);

        \Kirki::add_config(self::KIRKI_CONFIG, array(
            'capability'        => 'edit_theme_options',
            'option_type'       => 'theme_mod',
            'gutenberg_support' => false
        ));

        //Applicators [Stuff that make effect on the frontend]
        new \Municipio\Customizer\Applicators\Modifiers();
        new \Municipio\Customizer\Applicators\ControllerVariables();
        new \Municipio\Customizer\Applicators\Css();

        //Define panels
        new \Municipio\Customizer\Panels\Design();
        new \Municipio\Customizer\Panels\Component();
        new \Municipio\Customizer\Panels\Module();

        //Define panels with logic
        new \Municipio\Customizer\Panels\DesignLibrary();
    }
}
