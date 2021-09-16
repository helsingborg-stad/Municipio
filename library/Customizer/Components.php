<?php

namespace Municipio\Customizer;

/**
 * Class Design
 * @package Municipio\Customizer
 */
class Components extends Design
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
     * Components constructor.
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
                'id' => 'card', 
                'title' => "Card", 
                'description' => __('Detailed customization for cards.', 'municipio'),
                'render' => true,
                'share' => true,
                'active' => true
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
               'id' => 'component', 
               'title' => __('Components', 'municipio'),
            ],
            $this->configuration
        );
    }    
}
