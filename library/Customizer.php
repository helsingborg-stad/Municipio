<?php

namespace Municipio;

use Kirki\Compatibility\Kirki;
use Municipio\Customizer\PanelsRegistry;

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
        }, 11);

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

        add_filter('kirki_get_value', [$this, 'kirkiGetValue'], 11, 3);
        add_filter('kirki_values_get_value', [$this, 'kirkiValuesGetValue'], 11, 2);
    }

    public function kirkiGetValue($value, $option, $default)
    {
        return $this->sanitizeKirkiDefaultArrayValue($value, $default);
    }

    public function kirkiValuesGetValue($value, $field_id)
    {
        if (!isset(Kirki::$all_fields[$field_id])) {
            return $value;
        }

        $field = Kirki::$all_fields[$field_id];
        return $this->sanitizeKirkiDefaultArrayValue($value, $field['default'] ?? '');
    }

    public function sanitizeKirkiDefaultArrayValue($value, $default)
    {
        if ($value === '' && is_array($default)) {
            return $default;
        }

        return $value;
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

        if(function_exists('kirki_pro_load_controls')) {
            kirki_pro_load_controls(); 
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

        Kirki::add_config(self::KIRKI_CONFIG, array(
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

        //Define Typography Customizer
        new \Municipio\Customizer\Controls\Typography();

        PanelsRegistry::getInstance()->build();
    }
}
