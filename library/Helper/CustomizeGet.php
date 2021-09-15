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

  /**
   * Parses the acf config
   * @return \WP_Error|void
   */
  public static function getAcfCustomizerFields($configuration, $dataFieldStack = [])
  { 
    $themeMods = \Municipio\Helper\CustomizeGet::get(); 

    if (is_array($configuration) && !empty($configuration)) {
        
        foreach ($configuration as $configurationKey => $config) {

            //File path
            $configFile = MUNICIPIO_PATH . 'library/AcfFields/json/customizer-' . $config['id'] . '.json';

            //Read file
            if (file_exists($configFile) && $data = json_decode(file_get_contents($configFile))) {

                //File validation
                if (count($data) != 1) {
                    return new \WP_Error("Configuration file should not contain more than one group " . $config);
                }

                //Get first group
                $data = array_pop($data);

                //Validate that we have fields 
                if (isset($data->fields) && !empty($data->fields)) {
                    foreach ($data->fields as $fieldIndex => $field) {

                        // If field is a group, set default value as array with key values
                        if($field->type === "group") {
                            $field->default_value = array();

                            foreach ($field->sub_fields as $subfield) {
                                $field->default_value[$subfield->name] = $subfield->default_value;
                            }
                        }

                        $dataFieldStack[$config['id']][$fieldIndex] = [
                            $field->key => [
                                'name' => str_replace(['municipio_', '_'], ['', '-'], $field->name),
                                'default' => $field->default_value ?? '',
                                'value' => $themeMods[$field->key] ?? '',
                                'prepend' => $field->prepend ?? null,
                                'append' => $field->append ?? null
                            ]
                        ];
                    }
                }
            }
        }
    }

    return $dataFieldStack; 
  }
}    