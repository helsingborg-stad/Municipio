<?php

namespace Municipio\Customizer;

/**
 * Class Design
 * @package Municipio\Customizer
 */
class Design
{
    /**
     * @var
     */
    private $dataFieldStack;

    /**
     * @var array|string[]
     */
    private array $configurationFiles = [
        'Colors' => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-color.json',
        'Radius' => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-radius.json'
    ];

    /**
     * Design constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'initPanels'));
        add_action('wp_head', array($this, 'getAcfCustomizerFields'), 5); 
        add_action('wp_head', array($this, 'renderCssVariables'), 10);
    }

    /**
     * Inits a new panel structure.
     */
    public function initPanels()
    {
        new \Municipio\Helper\Customizer(
            __('Design', 'municipio'),
            array_flip($this->configurationFiles) 
        );
    }

    /**
     * Parses the acf config
     * @return \WP_Error|void
     */
    public function getAcfCustomizerFields()
    {
        if (is_array($this->configurationFiles) && !empty($this->configurationFiles)) {

            foreach ($this->configurationFiles as $key => $config) {

                $data = file_get_contents($config);
                $themeMods = get_theme_mod(sanitize_title($key)); 

                if (file_exists($config) && $data = json_decode($data)) {
                    if (count($data) != 1) {
                        return new \WP_Error("Configuration file should not contain more than one group " . $config);
                    }

                    $data = array_pop($data);

                    if (isset($data->fields) && !empty($data->fields)) {
                        foreach ($data->fields as $index => $field) {
                           
                            $this->dataFieldStack[sanitize_title($data->title)][$index] = [
                                $field->key => [
                                    'group-id'  => sanitize_title($data->title),
                                    'name'      => str_replace(['municipio_', '_'], ['', '-'], $field->name),
                                    'default'   => $field->default_value,
                                    'value'     => $themeMods[$field->key]
                                ]
                            ];
                        }
                    }

                } else {
                    return new \WP_Error("Could not read configuration file " . $config);
                }
            }
        }
    }

    /**
     * Render root css variables
     */
    public function renderCssVariables()
    {
        $inlineStyle = null;
        foreach($this->dataFieldStack as $key => $stackItem) {
            $inlineStyle .= PHP_EOL . '  /* Variables: ' . ucfirst($key) . ' */' . PHP_EOL;
            foreach($stackItem as $index => $prop) {
                $itemKey = array_key_first($stackItem[$index]);
                $inlineStyle .= '  --' . $prop[$itemKey]['name'] . ': ' . (!empty($prop[$itemKey]['value']) ?
                        $prop[$itemKey]['value'] : $prop[$itemKey]['default']) . ';' . PHP_EOL;
            }
        }

        wp_dequeue_style('municipio-css-vars');
        wp_register_style('municipio-css-vars', false);
        wp_enqueue_style('municipio-css-vars');
        wp_add_inline_style('municipio-css-vars', ":root {{$inlineStyle}}");
    }
}