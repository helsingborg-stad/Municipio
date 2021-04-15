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
    private $configurationFiles = [
        'Colors' => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-color.json',
        'Radius' => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-radius.json',
        'Modules' => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-modules.json',
        'Site' => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-site.json',
    ];

    /**
     * Design constructor.
     * @return void
     */
    public function __construct()
    {
        add_action('init', array($this, 'initPanels'));
        add_action('wp_head', array($this, 'getAcfCustomizerFields'), 5);
        add_action('wp_head', array($this, 'renderCssVariables'), 10);
        add_action('wp_head', array($this, 'moduleClasses'), 20);
    }

    /**
     * Inits a new panel structure.
     * @return void
     */
    public function initPanels()
    {
        if(!is_customize_preview() && !is_admin()) {
            return false; 
        }

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

            $themeMods = $this->getThemeMods(); 

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
                                    'group-id' => sanitize_title($data->title),
                                    'name' => str_replace(['municipio_', '_'], ['', '-'], $field->name),
                                    'default' => $field->default_value ?? '',
                                    'value' => $themeMods[$field->key] ?? '',
                                    'prepend' => $field->prepend ?? null,
                                    'append' => $field->append ?? null
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
        $inlineStyle = null;
        foreach ($this->dataFieldStack as $key => $stackItems) {

            $inlineStyle .= PHP_EOL . '  /* Variables: ' . ucfirst($key) . ' */' . PHP_EOL;

            foreach ($stackItems as $index => $prop) {
                $itemKey = key($stackItems[$index]);
                $inlineStyle .= '  --' . $prop[$itemKey]['name'] . ': ' .
                    (isset($prop[$itemKey]['prepend']) &&
                    !empty($prop[$itemKey]['prepend']) ? $prop[$itemKey]['prepend'] : null) .
                    (isset($prop[$itemKey]['value']) &&
                    !empty($prop[$itemKey]['value']) ? $prop[$itemKey]['value'] : $prop[$itemKey]['default']) .
                    (isset($prop[$itemKey]['append']) &&
                    !empty($prop[$itemKey]['append']) ? $prop[$itemKey]['append'] : null) . ';' . PHP_EOL;
            }
        }

        wp_dequeue_style('municipio-css-vars');
        wp_register_style('municipio-css-vars', false);
        wp_enqueue_style('municipio-css-vars');
        wp_add_inline_style('municipio-css-vars', ":root {{$inlineStyle}}");
    }

    /* Add options specified in customizer for modules */
    public function moduleClasses() {
        
        $moduleData = [];

        $dataStack  = array_merge($this->dataFieldStack['modules'], $this->dataFieldStack['site']);

        //Build array with context and it's classes
        if(is_array($dataStack) && !empty($dataStack)) {
            foreach($dataStack as $data) {
                foreach ($data as $key => $value) {
                    
                    //Get named parts
                    $nameParts = explode('-', $value['name']);

                    //Remove last element if array only has one value
                    if(count($nameParts) > 1) {
                        array_pop($nameParts);
                    }

                    //Create key parts
                    $Module = isset($nameParts[0]) ? $nameParts[0] : '';
                    $View   = isset($nameParts[1]) ? ucfirst($nameParts[1]) : '';

                    //Set value for key parts
                    $moduleData[$Module . $View] = !empty($value['value']) ? $value['value'] : $value['default'];
                
                }
            }
        }

        //Build filters
        $filters = [
            'ComponentLibrary/Component/Header/Modifier',
            'ComponentLibrary/Component/Card/Modifier'
        ];

        //Apply filter + function
        if(is_array($filters) && !empty($filters)) {
            foreach($filters as $filter) {
                add_filter($filter, function($modifiers, $contexts) use($moduleData) {
                    
                    if(!is_array($modifiers)) {
                        $modifiers = [];
                    }
        
                    //Always handle as array
                    if(!is_array($contexts)) {
                        $contexts = [$contexts]; 
                    }
        
                    //Create modifiers if exists
                    if(is_array($contexts) && !empty($contexts)) {
                        foreach($contexts as $key => $context) {

                            if(isset($moduleData[$context])) {
                                
                                if(!is_array($moduleData[$context])) {
                                    $moduleData[$context] = [$moduleData[$context]];
                                }
                
                                $modifiers = array_merge($modifiers, $moduleData[$context]);
                            }

                        }
                    }
        
                    return (array) $modifiers;
                    
                }, 10, 2); 
            }
        }
    }
    
    /**
     * Get the live value of theme mods
     *
     * @return array Array with theme mods
     */
    private function getThemeMods() {

        $themeMods = [];

        if(is_customize_preview()) {
            foreach((array) get_theme_mods() as $key => $mods) {
                $themeMods = array_merge($themeMods, (array) get_theme_mod($key)); 
            }
        } else {

            $storedThemeMods = get_theme_mods(); 

            if(array($storedThemeMods) && !empty($storedThemeMods)) {
                foreach($storedThemeMods as $mod) {
                    if(is_array($mod)) {
                        $themeMods = array_merge($themeMods, $mod); 
                    }
                }
            }
        }
        
        return $themeMods; 
    }
}
