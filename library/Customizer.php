<?php

namespace Municipio;

class Customizer
{
    public const KIRKI_CONFIG = "municipio_config";

    public static $panels = array();

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

        /**
         * Loads functionality to load a certain page
         * for each expanded panel.
         */
        add_action(
            'customize_controls_enqueue_scripts',
            array($this, 'addPreviewPageSwitches')
        );

        /**
         * Collects all panels view a preview url.
         */
        add_action('kirki_section_added', function ($id, $args) {
            if (isset($args['preview_url']) && filter_var($args['preview_url'], FILTER_VALIDATE_URL)) {
                self::$panels[$id] = $args['preview_url'];
            }
        }, 10, 2);
    }

    /**
     * Load embedded kirki
     *
     * @return void
     */
    public function loadEmbeddedKirki()
    {
        $kirkiFilePath = rtrim(MUNICIPIO_PATH, '/') . '/vendor/kirki/kirki.php';
        if (file_exists($kirkiFilePath)) {
            include_once($kirkiFilePath);
        }
    }

    /**
     * Initialize the Customizer
     *
     * @return void
     */
    public function addPreviewPageSwitches()
    {
        wp_register_script(
            'municipio-customizer-preview',
            get_template_directory_uri() . '/assets/dist/' . \Municipio\Helper\CacheBust::name('js/customizer-preview.js'),
            array( 'jquery', 'customize-controls' ),
            false,
            true
        );
        wp_localize_script(
            'municipio-customizer-preview',
            'customizerPanelPreviewUrls',
            (array) self::$panels
        );
        wp_enqueue_script('municipio-customizer-preview');
    }

    /**
     * Init customizer toolset
     *
     * @return void
     */
    public function init()
    {
        if (!defined("WEB_FONT_DISABLE_INLINE")) {
            define("WEB_FONT_DISABLE_INLINE", true);
        }

        \Kirki::add_config(self::KIRKI_CONFIG, array(
            'capability'        => 'edit_theme_options',
            'option_type'       => 'theme_mod',
            'gutenberg_support' => false
        ));

        // Custom fonts support (parse uploaded fonts)
        new \Kirki\Module\FontUploads();

        //Applicators [Stuff that make effect on the frontend]
        new \Municipio\Customizer\Applicators\Modifiers();
        new \Municipio\Customizer\Applicators\ComponentData();
        new \Municipio\Customizer\Applicators\ControllerVariables();
        new \Municipio\Customizer\Applicators\Css();

        //Define panels
        new \Municipio\Customizer\Panels\Design();
        new \Municipio\Customizer\Panels\Component();
        new \Municipio\Customizer\Panels\Module();
        new \Municipio\Customizer\Panels\Archive();
        new \Municipio\Customizer\Panels\Menu();

        //Define panels with logic
        new \Municipio\Customizer\Panels\DesignLibrary();
    }
}
