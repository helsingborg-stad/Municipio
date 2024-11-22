<?php

namespace Municipio;

use Kirki\Compatibility\Kirki;
use Municipio\Customizer\PanelsRegistry;
use WpService\WpService;
use wpdb;
use Municipio\Customizer\Applicators\Types\Modifier;
use Municipio\Customizer\Applicators\Types\Component;
use Municipio\Customizer\Applicators\Types\Controller;
use Municipio\Customizer\Applicators\Types\Css;

class Customizer
{
    public const KIRKI_CONFIG = "municipio_config";

    public static $panels = array();

    public function __construct(private WpService $wpService, private wpdb $wpdb)
    {
        //Load embedded kirki PRO
        $this->loadEmbeddedKirkiPro();

        //Kirki failed to load, handle
        $this->wpService->addAction('init', [$this, 'initKirki'], 11);

        //Loads functionality to load a certain page for each expanded panel.
        $this->wpService->addAction('customize_controls_enqueue_scripts', [$this, 'addPreviewPageSwitches']);

        //Collects all panels view a preview url.
        $this->wpService->addAction('kirki_section_added', function ($id, $args) {
            if (isset($args['preview_url']) && filter_var($args['preview_url'], FILTER_VALIDATE_URL)) {
                self::$panels[$id] = $args['preview_url'];
            }
        }, 10, 2);

        // Add filter to sanitize kirki default array values
        $this->wpService->addFilter('kirki_get_value', [$this, 'kirkiGetValue'], 11, 3);
        $this->wpService->addFilter('kirki_values_get_value', [$this, 'kirkiValuesGetValue'], 11, 2);
    }

    /**
     * Initialize Kirki
     * 
     * @return void
     */
    public function initKirki()
    {
        if (class_exists("Kirki")) {
            return $this->init();
        }

        $this->wpService->wpDie(
            $this->wpService->__("Kirki Customizer framework is required"),
            $this->wpService->__("Plugin install required"),
            [
                'link_url'  => "https://github.com/helsingborg-stad/kirki",
                'link_text' => $this->wpService->__("Install plugin", 'municipio')
            ]
        );
    }

    /**
     * Sanitize kirki default array values
     * 
     * @param mixed $value
     * @param string $option
     * @param mixed $default
     * 
     * @return mixed
     */
    public function kirkiGetValue($value, $option, $default)
    {
        return $this->sanitizeKirkiDefaultArrayValue($value, $default);
    }

    /**
     * Get kirki values
     * 
     * @param mixed $value
     * @param string $field_id
     * 
     * @return mixed
     */
    public function kirkiValuesGetValue($value, $field_id)
    {
        if (!isset(Kirki::$all_fields[$field_id])) {
            return $value;
        }

        $field = Kirki::$all_fields[$field_id];
        return $this->sanitizeKirkiDefaultArrayValue($value, $field['default'] ?? '');
    }

    /**
     * Sanitize kirki default array values
     * 
     * @param mixed $value
     * @param mixed $default
     * 
     * @return mixed
     */
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
    public function loadEmbeddedKirkiPro()
    {
        if (function_exists('kirki_pro_load_controls')) {
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
        Kirki::add_config(self::KIRKI_CONFIG, array(
            'capability'        => 'edit_theme_options',
            'option_type'       => 'theme_mod',
            'gutenberg_support' => false,
            'disable_output'    => true
        ));

        // Custom fonts support (parse uploaded fonts)
        if (class_exists('\Kirki\Module\FontUploads')) {
            new \Kirki\Module\FontUploads();
        }

        //Applicators [Applies settings on the frontend]
        $this->initApplicators();

        //Define Typography Customizer
        new \Municipio\Customizer\Controls\Typography();

        PanelsRegistry::getInstance()->build();
    }

    /**
     * Initialize applicators
     * This will apply settings from the customizer on the frontend.
     * This also includes a cacing layer, to reduce the amount of
     * time spent on calculating the settings.
     *
     * @return void
     */
    public function initApplicators()
    {
        $applicators = [
            new Controller($this->wpService),
            new Modifier($this->wpService),
            new Component($this->wpService),
            new Css($this->wpService)
        ];

        $customizerCache = new \Municipio\Customizer\Applicators\ApplicatorCache(
            $this->wpService,
            $this->wpdb,
            ...$applicators
        );

        $customizerCache->addHooks();
    }
}
