<?php

namespace Municipio;

use Municipio\Customizer\Applicators\ApplicatorInterface;
use Municipio\Customizer\Applicators\Types\Component;
use Municipio\Customizer\Applicators\Types\Controller;
use Municipio\Customizer\Applicators\Types\Modifier;
use Municipio\Customizer\PanelsRegistry;
use wpdb;
use WpService\WpService;

class Customizer
{
    public const CONFIG = 'municipio_config';

    public static $panels = array();

    public function __construct(
        private WpService $wpService,
        private wpdb $wpdb,
    ) {
        $this->wpService->addAction('init', [$this, 'init'], 11);

        //Loads functionality to load a certain page for each expanded panel.
        $this->wpService->addAction('customize_controls_enqueue_scripts', [$this, 'addPreviewPageSwitches']);
    }

    /**
     * Sanitize default array values.
     *
     * @param mixed $value
     * @param mixed $default
     *
     * @return mixed
     */
    public function sanitizeDefaultArrayValue($value, $default)
    {
        if ($value === '' && is_array($default)) {
            return $default;
        }

        return $value;
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
            array('jquery', 'customize-controls'),
            false,
            true,
        );
        wp_localize_script(
            'municipio-customizer-preview',
            'customizerPanelPreviewUrls',
            (array) self::$panels,
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
        //Applicators [Applies settings on the frontend]
        $this->initApplicators();

        //Build panels and sections
        PanelsRegistry::getInstance()->build();
    }

    /**
     * Initialize applicators
     * This will apply settings from the customizer on the frontend.
     *
     * @return void
     */
    public function initApplicators()
    {
        $this->wpService->addAction('wp', [$this, 'applyApplicators'], 5);
        $this->wpService->addAction('rest_api_init', [$this, 'applyApplicators'], 5);
    }

    /**
     * Apply customizer applicators on supported requests.
     *
     * @return void
     */
    public function applyApplicators(): void
    {
        if (!$this->isFrontend()) {
            return;
        }

        foreach ($this->getApplicators() as $applicator) {
            $data = $applicator->getData();

            if (!is_array($data) && !is_object($data)) {
                continue;
            }

            $applicator->applyData($data);
        }
    }

    /**
     * Get applicators used for customizer output.
     *
     * @return array<int, ApplicatorInterface>
     */
    private function getApplicators(): array
    {
        return [
            new Controller($this->wpService),
            new Modifier($this->wpService),
            new Component($this->wpService),
        ];
    }

    /**
     * Check if the current request is a frontend request.
     *
     * @return bool
     */
    private function isFrontend(): bool
    {
        return !is_admin() && !defined('WP_CLI') && !defined('WP_IMPORTING') && !defined('WP_INSTALLING');
    }
}
