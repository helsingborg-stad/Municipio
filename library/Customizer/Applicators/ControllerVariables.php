<?php

namespace Municipio\Customizer\Applicators;

class ControllerVariables
{
  public function __construct() {
    add_filter('Municipio/Controller/Customizer', array($this, 'get')); 
  }

  /**
   * Get customizer controller variables
   *
   * @param array $stack  External stack objects
   * 
   * @return object
   */
  public function get($stack = []) {

    //Get field definition
    $fields = \Kirki::$fields; 

    //Determine what's a controller var, fetch it
    if(is_array($fields) && !empty($fields)) {
        foreach($fields as $key => $field) {
            if(isset($field['args']['output']['type']) && $field['args']['output']['type'] === 'controller') {
                $stack[$key] = \Kirki::get_option($key);
            }
        }
    }

    //Camel case response keys, and return
    return \Municipio\Helper\FormatObject::camelCase(
        (object) $stack
    ); 
  }

}