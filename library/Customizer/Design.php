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
    private $dataFieldStack;    //Stores the stack between calc & render (runs on hooks)

    /**
     * @var array|null[]
     */
    private $configuration = null;  //Fill in construct due to translations

    /**
     * Design constructor.
     * @return void
     */
    public function __construct()
    {
        /**
         * Field configuration is always 
         * feteched by filename: 'customizer-{$id}.json' in 
         * MUNICIPIO_PATH . 'library/AcfFields/json/
         */
        $this->configuration = [
            [
                'id' => 'site', 
                'title' => "Site", 
                'description' => __('General appearance site settings', 'municipio'),
                'render' => true,
                'share' => true,
                'active' => true
            ],
            [
                'id' => 'font', 
                'title' => "Fonts", 
                'description' => __('Select font faces', 'municipio'),
                'render' => false,
                'share' => true,
                'active' => false
            ],
            [
                'id' => 'color', 
                'title' => "Colors", 
                'description' => __('Adjust base colors for the site', 'municipio'),
                'render' => true,
                'share' => true,
                'active' => true
            ],
            [
                'id' => 'radius', 
                'title' => "Radius", 
                'description' => '',
                'render' => true,
                'share' => true,
                'active' => true
            ],
            [
                'id' => 'width', 
                'title' => "Widths", 
                'description' => '',
                'render' => true,
                'share' => true,
                'active' => true
            ],
            [
                'id' => 'padding', 
                'title' => "Padding", 
                'description' => '',
                'render' => true,
                'share' => true,
                'active' => true
            ],
            [
                'id' => 'borders', 
                'title' => "Borders", 
                'description' => __('Adjust apperance of borders in the design.', 'municipio'),
                'render' => true,
                'share' => true,
                'active' => false
            ],
            [
                'id' => 'shadows', 
                'title' => "Shadows", 
                'description' => __('Adjust apperance of shadows in the design.', 'municipio'),
                'render' => true,
                'share' => true,
                'active' => false
            ],
            [
                'id' => 'header', 
                'title' => "Header", 
                'description' => __('Set header apperance in the design.', 'municipio'),
                'render' => true,
                'share' => true,
                'active' => false
            ],
            [
                'id' => 'footer', 
                'title' => "Footer", 
                'description' => __('Set footer apperance in the design.', 'municipio'),
                'render' => true,
                'share' => true,
                'active' => false
            ],
            [
                'id' => 'article', 
                'title' => "Article", 
                'description' => __('Set apperance of the article in the design.', 'municipio'),
                'render' => true,
                'share' => true,
                'active' => false
            ]
        ];

        //Adds panels to customizer area 
        add_action('init', array($this, 'initPanel'));
        
        //Get configurations
        add_action('wp_head', array($this, 'getAcfCustomizerFields'), 5);

        //Render css
        add_action('wp_head', array($this, 'renderCssVariables'), 30);
    }

    /**
     * Inits a new panel structure.
     * @return void
     */
    public function initPanel()
    {
        //Only init panels incustomizer
        if(!is_customize_preview() && !is_admin()) {
            return false; 
        }

        //Add panels & fields 
        new \Municipio\Helper\CustomizeCreate(
            [
               'id' => 'design', 
               'title' => __('Design', 'municipio')
            ],
            $this->configuration
        );
    }

    /**
     * Store acf fields in stack
     */
    public function getAcfCustomizerFields() {
        $this->dataFieldStack = \Municipio\Helper\CustomizeGet::getAcfCustomizerFields(
            $this->configuration
        );
    }

    /**
     * Render root css variables
     * @return void
     */
    public function renderCssVariables()
    {

        $inlineStyle = null;

        foreach ($this->configuration as $config) {

            //Only add if allowed to render & active
            if($config['render'] !== true || $config['active'] !== true) {
                continue;
            }

            //Only add if defined
            if(!isset($this->dataFieldStack[$config['id']])) {
                continue;
            }

            //Get stack
            $stackItems = $this->dataFieldStack[$config['id']];

            //Init section
            $inlineStyle .= PHP_EOL . '  /* css-var: ' . $config['id'] . ' */' . PHP_EOL;

            if(is_array($stackItems) && !empty($stackItems)) {

                foreach ($stackItems as $index => $prop) {

                    $itemKey    = key($stackItems[$index]);
                    $propItem   = $prop[$itemKey];

                    //Handle color
                    if($config['id'] === 'color') {
                        $propItem['value'] = \Municipio\Helper\Color::prepareColor($propItem);                                    
                    }

                    //Handle width
                    if($config['id'] === 'width') {

                        if(!in_array($propItem['name'], ['container-width-content'])) {

                            //Do not render archive on any other page
                            if(!is_archive() && $propItem['name'] == "container-width-archive") {
                                continue;
                            }

                            //Do not render fontpage width on any other page
                            if((!is_front_page() && !is_home()) && $propItem['name'] == "container-width-frontpage") {
                                continue;
                            }

                            //Do not render default container width on special pagetypes
                            if((is_archive()||is_front_page()||is_home()||is_tax()) && $propItem['name'] == "container-width") {
                                continue;
                            }

                            //Use archive prop or frontpage prop as container-width
                            if(substr($propItem['name'], 0, strlen("container-width")) == "container-width") {
                                $propItem['name'] = "container-width";           
                            }

                        }
                        
                    }

                    /** Add append & prepent values, incl. defaults */
                    $inlineStyle .= \Municipio\Helper\CustomizeGet::createCssVar(
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
}
