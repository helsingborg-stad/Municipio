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
        'Colors'    => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-color.json',
        'Radius'    => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-radius.json',
        'Modules'   => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-modules.json',
        'Site'      => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-site.json',
        'Widths'    => MUNICIPIO_PATH . 'library/AcfFields/json/customizer-width.json',
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
                
                if (file_exists($config) && $data = json_decode($data)) {

                    if (count($data) != 1) {
                        return new \WP_Error("Configuration file should not contain more than one group " . $config);
                    }

                    $data = array_pop($data);

                    if (isset($data->fields) && !empty($data->fields)) {
                        foreach ($data->fields as $index => $field) {

                            // If field is a group, set default value as array with key values
                            if($field->type === "group") {
                                $field->default_value = array();

                                foreach ($field->sub_fields as $subfield) {
                                    $field->default_value[$subfield->name] = $subfield->default_value;
                                }
                            }

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

        $cssOptions = ['colors', 'radiuses', 'site-width'];

        $inlineStyle = null;
        foreach ($cssOptions as $key) {
            $stackItems = $this->dataFieldStack[$key];

            $inlineStyle .= PHP_EOL . '  /* Variables: ' . ucfirst($key) . ' */' . PHP_EOL;

            if(is_array($stackItems) && !empty($stackItems)) {
                foreach ($stackItems as $index => $prop) {

                    $itemKey = key($stackItems[$index]);
                    $propItem = $prop[$itemKey];

                    //Handle colors
                    if($key === 'colors') {
                        $colors = new Colors();
                        $propItem['value'] = $colors->prepareColor($propItem);                                    
                    } 

                    //Handle width
                    if($key === 'site-width') {

                        if(!in_array($propItem['name'], ['container-width-content'])) {

                            if(!is_archive() && $propItem['name'] == "container-width-archive") {
                                continue;
                            }

                            if(!is_front_page() && $propItem['name'] == "container-width-frontpage") {
                                continue;
                            }

                            if((is_archive()||is_front_page()) && $propItem['name'] == "container-width") {
                                continue;
                            }

                            //Use archive prop or frontpage prop as container-width
                            if(substr($propItem['name'], 0, strlen("container-width")) == "container-width") {
                                $propItem['name'] = "container-width";           
                            }

                        }
                        
                    }

                    $inlineStyle .= $this->filterValue(
                        $propItem['name'],
                        $propItem['prepend'],
                        $propItem['value'],
                        $propItem['append'],
                        $propItem['default']                 
                    );
                }
            }
        }

        wp_dequeue_style('municipio-css-vars');
        wp_register_style('municipio-css-vars', false);
        wp_enqueue_style('municipio-css-vars');
        wp_add_inline_style('municipio-css-vars', ":root {{$inlineStyle}}");
    }

    public function filterValue($name, $prepend = '', $value, $append = '', $default) {
        $value = !empty($value) ? $value : $default;
       
        return '  --' . $name . ': ' . $prepend . $value . $append . ';' . PHP_EOL;
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
            'ComponentLibrary/Component/Card/Modifier',
            'ComponentLibrary/Component/Segment/Modifier'
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
