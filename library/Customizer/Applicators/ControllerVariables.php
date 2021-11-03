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

              /**
               * Implementation of output active_callback functionality for output. 
               * Can handle multiple AND statements. 
               */
              if(is_string($field['args']['active_callback']) && $field['args']['active_callback'] === '__return_true') {
                $stack[$key] = \Kirki::get_option($key);
              } elseif(is_string($field['args']['active_callback']) && $field['args']['active_callback'] === '__return_false') {
                $stack[$key] = null;
              } elseif(is_array($field['args']['active_callback']) && !empty($field['args']['active_callback'])) {

                $shouldReturn = false; 

                foreach($field['args']['active_callback'] as $cb) {
                  $cb = (object) $cb;
                  if(eval('return \Kirki::get_option("' . $cb->setting . '") ' . $cb->operator . ' "' . $cb->value . '";')) {
                      $shouldReturn = true; 
                  }
                }

                if($shouldReturn === true) {
                  $stack[$key] = \Kirki::get_option($key);
                } else {
                  $stack[$key] = null;
                }
              }
              
            }
        }
    }

    //Camel case response keys, and return
    return \Municipio\Helper\FormatObject::camelCase(
        (object) $stack
    ); 
  }

}