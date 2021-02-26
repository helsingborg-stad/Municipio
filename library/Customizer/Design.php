<?php

namespace Municipio\Customizer;

class Design
{

  private $dataFieldStack; 
  private $configurationFiles = [
    MUNICIPIO_PATH . 'library/AcfFields/json/customizer-color.json'
  ];

  public function __construct() {
    add_action('init', array($this, 'initPanels'));


    $this->readAcfFields(); 


    //add_action('wp_head', '')
  }

  public function readAcfFields() {
    
    if(is_array($this->configurationFiles) && !empty($this->configurationFiles)) {
      foreach($this->configurationFiles as $config) {
        $data = file_get_contents($config); 

        if($data = json_decode($data)) {
          
          if(count($data) != 1) {
            return new \WP_Error("Counfiguration file should not contain more than one group " . $config); 
          }

          //Gets first group
          $data = array_pop($data);

          if(isset($data->fields) && !empty($data->fields)) {
            foreach($data->fields as $field) {
              var_dump($field); 
            }
          }

        } else {
          return new \WP_Error("Could not read configuration file " . $config); 
        }
      }
    }
  }

  /**
   * Inits a new panel structure.
   *
   * @return void
   */
  public function initPanels() {
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
   * Render root css variables
   *
   * @return void
   */
  public function renderCssVariables() {

    $data = [
      'colorprofile' => array(
        'field_60361bcb76325' => ['name' => 'primary-color', 'default' => '#000'],
        'field_60364d06dc120' => ['name' => 'secondary-color', 'default' => '#000']
      )
    ]; 

    if(is_array($data) && !empty($data)) {
      
      echo '<style>'. PHP_EOL;
        echo ':root {'. PHP_EOL; 
        
          foreach($data as $key => $item) {

            //Get the theme mods
            $settings = get_theme_mod($key); 

            //Print heading
            if(is_array($settings) && !empty($settings)) {
              echo PHP_EOL . '  /* Variables: ' . $key . ' */' . PHP_EOL; 
            }

            //Render settings
            if(is_array($settings) && !empty($settings)) {
              foreach($settings as $identifier => $value) {
                echo '  --' . $item[$identifier]['name'] . ': ' . (!empty($value) ? $value : $item[$identifier]['default']) . ';' . PHP_EOL; 
              }
            }

          }
        
        echo PHP_EOL . '}'. PHP_EOL; 
      echo '</style>'. PHP_EOL; 

    }
  }
}