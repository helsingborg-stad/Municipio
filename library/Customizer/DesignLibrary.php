<?php

namespace Municipio\Customizer;

class DesignLibrary
{
    private $apiUrl         = 'https://customizer.helsingborg.io/';

    private $sharedModKeys  = [
        'colors',
        'radius'
    ]; 
    
    private $apiActions     = [ 
        'post'  => 'update' . DIRECTORY_SEPARATOR,
        'single' => 'data' . DIRECTORY_SEPARATOR
    ];  

    private $uniqid;

    private $design;

    public function __construct()
    {

        //Cachebust id
        $this->uniqid = uniqid(); 

        //Forget setting of load design field
        add_action('customize_save', function($manager) {
            remove_theme_mod('loaddesign');
        }); 

        //Removes the ability to move back from designlibrary [TODO: FIX. NOT WORKING!]
        add_action('wp_head', function() {
            echo '<style>#sub-accordion-section-loaddesign .customize-section-back {display: none !important;}</style>'; 
        }); 

        //Store on save
        add_action('customize_save_after', array($this, 'storeThemeMod'));

        //Cron action to trigger
        add_action('municipio_store_theme_mod', array($this, 'storeThemeMod'));

        //Add visual panel
        add_action('init', function() {
            new \Municipio\Helper\Customizer(
                __('Designlibrary', 'municipio'),
                ['Load design']
            );
        }); 

        //Cron to update design periodically
        add_action('admin_init', function() {
            if (!wp_next_scheduled( 'municipio_store_theme_mod')) {
                wp_schedule_event(time(), 'weekly', 'municipio_store_theme_mod');
            }
        }); 

        //Add options in design loader
        add_filter('acf/load_field/name=customizer_select_designshare', array($this, 'populateDesignSelect')); 

        //Loop all settingspanels and load api values
        if(is_array($this->sharedModKeys) && !empty($this->sharedModKeys)) {
            foreach($this->sharedModKeys as $mod) {
                add_filter("theme_mod_" . $mod, array($this, 'loadThemeMod')); 
            }
        }
        
    }

    /**
     * Makes the switcheroo between whats locally and whats in the api. 
     *
     * @param   mixed   $value  The local value
     * @return  mixed   $value  The new replaced value
     */
    public function loadThemeMod($value) {
        
        $design = get_theme_mod('loaddesign');

        //Get settings name
        $name = end(explode("_", current_filter())); 

        if(is_array($design) && !empty($design)) {
            $design = array_pop($design); 

            $data = wp_remote_get(
                $this->apiUrl . 
                $this->apiActions['single'] . 
                $design . 
                ".json",
                ['cacheBust' => $this->uniqid]
            );

            if($json = json_decode($data['body'])) { 
                return (array) $json->mods->{$name}; 
            }
        }

        return $value;
    }

    /**
     * Populates the design dropdown with avabile designs from api
     *
     * @param   array   $field  Without options
     * @return  array   $field  Appended options array
     */
    public function populateDesignSelect($field) {

        //Fetch data from host
        $data = wp_remote_get($this->apiUrl, ['cacheBust' => $this->uniqid]); 
        
        if(isset($data['body'])) {

            $choices = json_decode($data['body']); 

            //Populate select
            if( is_array($choices) ) {
                foreach( $choices as $choice ) {
                    $field['choices'][ $choice->uuid ] = $choice->name . " (" . $choice->website. " )";   
                }
            }

        } else {
            $field['choices']['error'] = __("Error loading options", 'muncipio'); 
        } 
        
        return $field;
    }

    /**
     * Requests to store the theme mod in api
     *
     * @param Object|null $customizerManager
     * @return bool|WP_Error
     */
    public function storeThemeMod($customizerManager = null) {
        
        $response = wp_remote_post(
            $this->apiUrl . 
            $this->apiActions['post'] . 
            '?cacheBust=' . $this->uniqid, 
            [
                'method' => 'POST',
                'timeout' => 5,
                'body' => $this->getSiteData(),
            ]
        );

        if (is_wp_error($response)) {
            return new WP_Error($response->get_error_message()); 
        } else {
            if(isset($response['body']) && !empty($response['body'])) {
                return true; 
            }
            exit; 
        }

        return new WP_Error("Could not store design in designshare."); 
    } 

    /**
     * Get the data about this installation
     *
     * @return array Array containing site data
     */
    private function getSiteData() {
        return [
            'uuid' => md5(ABSPATH . get_home_url()),
            'website' => get_home_url(),
            'name' => get_bloginfo('name'),
            'mods' => $this->getSharedAttributes(),
        ]; 
    }

    /**
     * Get the attributes in theme mod to be shared
     *
     * @param   array $stack    Empty stack array
     * @return  array $stack    Populated stack array
     */
    private function getSharedAttributes($stack = []) {
        
        $mods = get_theme_mods(); 

        if(is_array($mods) && !empty($mods)) {
            foreach($mods as $key => $mod) {
                if(in_array($key, $this->sharedModKeys)) {
                    $stack[$key] = $mod;  
                }
            }
        }

        return $stack; 
    }
}
