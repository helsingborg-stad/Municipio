<?php

namespace Municipio;

use Kirki\Compatibility\Kirki;
use Kirki\Panel;
use Municipio\Customizer\KirkiPanel;
use Municipio\Customizer\PanelArgs;

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

        // Menus
        $menuPanel = new KirkiPanel('nav_menus');

        $designPanel = new KirkiPanel('municipio_customizer_panel_design', array(
            'title'       => esc_html__('General Apperance', 'municipio'),
            'description' => esc_html__('Manage site general design options.', 'municipio'),
            'priority'    => 120,
        ));
        
        $componentPanel = new KirkiPanel('municipio_customizer_panel_design_component', array(
            'title'       => esc_html__('Component Apperance', 'municipio'),
            'description' => esc_html__('Manage design options on component level.', 'municipio'),
            'priority'    => 120,
        ));
        
        $modulePanel = new KirkiPanel('municipio_customizer_panel_design_module', array(
            'title' =>  esc_html__('Module Apperance', 'municipio'),
            'description' =>  esc_html__('Module Apperance', 'municipio'),
            'priority' =>  120,
        ));
        
        $archivePanel = new KirkiPanel('municipio_customizer_panel_archive', array(
            'title' =>  esc_html__('Archive Apperance', 'municipio'),
            'description' =>  esc_html__('Manage apperance options on archives.', 'municipio'),
            'priority' =>  120,
        ));
        
        $designLibraryPanel = new KirkiPanel('municipio_customizer_panel_designlib', array(
            'title' =>  esc_html__('Design Library', 'municipio'),
            'description' =>  esc_html__('Select a design made by other municipio users.', 'municipio'),
            'priority' =>  1000,
        ));
        
        new \Municipio\Customizer\Panels\Design($designPanel->getID());
        new \Municipio\Customizer\Panels\Component($componentPanel->getID());
        new \Municipio\Customizer\Panels\Module($modulePanel->getID());
        new \Municipio\Customizer\Panels\Archive($archivePanel->getID());
        new \Municipio\Customizer\Sections\Menu($menuPanel->getID());
        new \Municipio\Customizer\Sections\LoadDesign($designLibraryPanel->getID());

        //Define Typography Customizer
        new \Municipio\Customizer\Controls\Typography();
    }
}
