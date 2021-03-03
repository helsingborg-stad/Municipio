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
    private $customizeSections;

    /**
     * @var array|string[]
     */
    private array $configurationFiles = [
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
     * Populate customizeSections with section names
     * @param $data
     */
    public function setSectionsArray($data){
        foreach($data as $dataKey => $dataValue){
            $this->customizeSections = (array) $this->customizeSections;
            if (!in_array($dataValue->location[0][0]->value, $this->customizeSections)) {
                $this->customizeSections[] = $dataValue->location[0][0]->value;
            }
        }
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

                    $this->setSectionsArray($data);

                    if (count($data) != 1) {
                        return new \WP_Error("Configuration file should not contain more than one group " . $config);
                    }

                    $data = array_pop($data);

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
     * Get Theme section values
     * @return array
     */
    public function getThemeModData() {
        $themeMods = [];
        foreach($this->customizeSections as $key => $sections){
            $themeMods[$key] = get_theme_mod($sections);
        }
        return $themeMods;
    }

    /**
     * Render root css variables
     * @return void
     */
    public function renderCssVariables()
    {
        $themeMods = $this->getThemeModData();

        if (is_array($this->dataFieldStack) && !empty($this->dataFieldStack)) {

            $inlineStyle = null;
            foreach ($this->dataFieldStack as $profileKey => $cssVariableDefinition) {

                if (is_array($cssVariableDefinition) && !empty($cssVariableDefinition)) {

                    if (is_array($cssVariableDefinition) && !empty($cssVariableDefinition)) {
                        $inlineStyle .= PHP_EOL . '  /* Variables: ' . $profileKey . ' */' . PHP_EOL;
                    }

                    foreach ($cssVariableDefinition as $key => $definition) {

                        $dbSetting = $themeMods[$key][array_key_first($definition)];
                        $defaults = array_pop($definition);
                        $inlineStyle .= '  --' . $defaults['name'] . ': ' . (!empty($dbSetting) ?
                                $dbSetting : $defaults['default']) . ';' . PHP_EOL;
                    }
                }
            }

            echo "<pre>";
            var_dump($inlineStyle);
            echo "</pre>";

            wp_dequeue_style('municipio-css-vars');
            wp_register_style('municipio-css-vars', false);
            wp_enqueue_style('municipio-css-vars');
            wp_add_inline_style('municipio-css-vars', ":root {{$inlineStyle}}");
        }

    }
}