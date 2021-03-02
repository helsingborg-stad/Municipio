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
     * @var string[]
     */
    private $configurationFiles = [
        MUNICIPIO_PATH . 'library/AcfFields/json/customizer-color.json',
        MUNICIPIO_PATH . 'library/AcfFields/json/customizer-color.json',
        MUNICIPIO_PATH . 'library/AcfFields/json/customizer-radius.json'
    ];

    /**
     * Design constructor.
     */
    public function __construct()
    {
        $this->getAcfCustomizerFields();
        add_action('init', array($this, 'initPanels'));
        add_action('wp_head', array($this, 'renderCssVariables'), 0);
    }

    /**
     * Inits a new panel structure.
     */
    public function initPanels()
    {
        new \Municipio\Helper\Customizer(
            __('Design', 'municipio'),
            [
                __('Colors', 'municipio'),
                __('Fonts', 'municipio'),
                __('Borders', 'municipio'),
                __('Radius', 'municipio')
            ]
        );
    }


    /**
     * Parses the acf config
     * @return \WP_Error|void
     */
    public function getAcfCustomizerFields()
    {
        if (is_array($this->configurationFiles) && !empty($this->configurationFiles)) {

            foreach ($this->configurationFiles as $int => $config) {
                $data = file_get_contents($config);

                if (file_exists($config) && $data = json_decode($data)) {

                    if (count($data) != 1) {
                        return new \WP_Error("Configuration file should not contain more than one group " . $config);
                    }

                    if (isset($data->fields) && !empty($data->fields)) {
                        foreach ($data->fields as $index => $field) {
                            $this->dataFieldStack[sanitize_title($data->title)][$index] = [
                                $field->key => [
                                    'group-id' => sanitize_title($data->title),
                                    'name' => str_replace(['municipio_', '_'], ['', '-'], $field->name),
                                    'default' => $field->default_value
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
     * @return void
     */
    public function renderCssVariables()
    {
        //Get the theme mods
        $themeMods = array_collapse(get_theme_mods());

        if (is_array($this->dataFieldStack) && !empty($this->dataFieldStack)) {

            $inlineStyle = null;

            foreach ($this->dataFieldStack as $profileKey => $cssVariableDefinition) {
                if (is_array($cssVariableDefinition) && !empty($cssVariableDefinition)) {

                    //Heading Comment
                    if (is_array($cssVariableDefinition) && !empty($cssVariableDefinition)) {
                        $inlineStyle .= PHP_EOL . '  /* Variables: ' . $profileKey . ' */' . PHP_EOL;
                    }

                    //Build CSS for print
                    foreach ($cssVariableDefinition as $id => $definition) {

                        $dbSetting = $themeMods[array_key_first($definition)];
                        $defaults = array_pop($definition);
                        $inlineStyle .= '  --' . $defaults['name'] . ': ' . (!empty($dbSetting) ?
                                $dbSetting : $defaults['default']) . ';' . PHP_EOL;
                    }
                }
            }

            wp_register_style('municipio-css-vars', false);
            wp_enqueue_style('municipio-css-vars');
            wp_add_inline_style('municipio-css-vars', ":root {{$inlineStyle}}");
        }
    }
}