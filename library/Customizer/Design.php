<?php

namespace Municipio\Customizer;

class Design
{
  private $instanceName; 
  private $InstanceSlug; 

  private $panelId = null;
  private $panels = [];
  private $name = null;

  public function __construct($name = "") {
    add_action('init', array($this, 'initPanels')); 
  }

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