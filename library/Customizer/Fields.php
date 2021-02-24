<?php

namespace Municipio\Customizer;

class Fields
{

  public function __construct() {
    add_action('wp_head', array($this, 'renderCssVars'));
    add_action('wp_head', array($this, 'test'));
    add_action('init', array($this, 'registerPanels'));
  }

  /**
   * Register settings panels
   *
   * @return void
   */
  public function registerPanels() {

    $panel_id = acf_add_customizer_panel(array(
        'title'        => 'Design',
    ));

    acf_add_customizer_section(array(
      'title'        => 'Color Profile',
      'storage_type' => 'theme_mod',
      'panel'        => $panel_id,
    ));
    
    acf_add_customizer_section(array(
      'title'        => 'Radiuses',
      'storage_type' => 'theme_mod',
      'panel'        => $panel_id,
    ));
  }

  /**
   * Render root css variables
   *
   * @return void
   */
  public function renderCssVars() {

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

  public function test() {
    echo '<style>';
    echo '.c-header.c-header--business .c-header__menu.c-header__menu--secondary, .c-card--panel .c-card__header, .c-button__filled--primary {background-color: var(--primary-color);}';
    echo '</style>'; 
  }
}