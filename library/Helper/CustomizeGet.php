<?php

namespace Municipio\Helper;

class CustomizeGet
{
  /**
   * Get the live value of theme mods
   *
   * @return array Array with theme mods
   */
  public static function get() {

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

  /**
   * Create css var printout
   *
   * @param string $name      Name of the css variable
   * @param string $prepend   If something should be prepended before val
   * @param string $value     The value
   * @param string $append    If something should be appended to the value
   * @param string $default   Default value
   * 
   * @return string Css var   The newly created css variable
   */
  public static function createCssVar($name, $prepend = '', $value, $append = '', $default) {

      $value = !is_null($value) ? $value : $default;
    
      return '  --' . $name . ': ' . $prepend . $value . $append . ';' . PHP_EOL;
  }

}    