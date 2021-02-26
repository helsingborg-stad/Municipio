<?php

namespace Municipio\Customizer;

class Design
{

  private $dataFieldStack; 
  private $configurationFiles = [
    MUNICIPIO_PATH . 'library/AcfFields/json/customizer-color.json',
    MUNICIPIO_PATH . 'library/AcfFields/json/customizer-radius.json'
  ];

  public function __construct() {

    $this->readAcfFields(); 

    add_action('init', array($this, 'initPanels'));
    add_action('wp_head', array($this, 'renderCssVariables'));
  }

  /**
   * Parses the acf config
   *
   * @return void
   */
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
              $this->dataFieldStack[sanitize_title($data->title)][] = [
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

    //Get the theme mods
    $themeMods = array_collapse(get_theme_mods()); 

    if(is_array($this->dataFieldStack) && !empty($this->dataFieldStack)) {

      echo '<style>'. PHP_EOL;
        echo ':root {'. PHP_EOL; 

          foreach($this->dataFieldStack as $profileKey => $cssVariableDefinition) {
            if(is_array($cssVariableDefinition) && !empty($cssVariableDefinition)) {

              //Print heading
              if(is_array($cssVariableDefinition) && !empty($cssVariableDefinition)) {
                echo PHP_EOL . '  /* Variables: ' . $profileKey . ' */' . PHP_EOL; 
              }

              //Print css
              foreach($cssVariableDefinition as $id => $definition) {

                $dbSetting  = $themeMods[array_key_first($definition)];
                $defaults   = array_pop($definition);

                echo '  --' . $defaults['name'] . ': ' . (!empty($dbSetting) ? $dbSetting : $defaults['default']) . ';' . PHP_EOL; 
              }
            }

          }
        
        echo PHP_EOL . '}'. PHP_EOL; 
      echo '</style>'. PHP_EOL; 

    }
  }
}