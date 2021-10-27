<?php

namespace Municipio\Helper;

class Customize
{ 

  private $themeMods = null; //Instance cache

  /**
   * Mock
   */
  public function __construct() {

    
    add_action('wp', function() {
      //var_dump($this->get());
    });
  }

  /**
   * Get array of theme options, where default values are set. 
   *
   * @param array $response
   * @return void
   */
  private function get($response = []) {
    
    global $wp_customize;
    var_dump($customize); 

    $settings = $this->accessProtected($wp_customize, 'settings'); 

    if(is_array($settings) && !empty($settings)) {
      foreach ($settings as $key => $setting) {
        $response[$key] = (object) [
          'type'  => $setting->theme_supports,
          'value' => $this->getThemeMods()[$key] ?? $setting->default
        ];
      }
    }

    return $response;
  }

  /**
   * Get theme mods
   *
   * @return array
   */
  private function getThemeMods() {
    if(isset($this->themeMods) && !is_null($this->themeMods)) {
      return $this->themeMods; 
    }
    return $this->themeMods = get_theme_mods(); 
  }

  /**
   * Access protected/private data in class
   *
   * @param object $obj
   * @param string $prop
   * @return mixed
   */
  public function accessProtected($obj, $prop) {
    $reflection = new \ReflectionClass($obj);
    $property = $reflection->getProperty($prop);
    $property->setAccessible(true);
    return $property->getValue($obj);
  }
}