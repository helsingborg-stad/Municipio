<?php

namespace Municipio\Customizer\Applicators;

class ControllerVariables
{
    public function __construct()
    {
        add_filter('Municipio/Controller/Customizer', array($this, 'get'));
    }

  /**
   * Get customizer controller variables
   *
   * @param array $stack  External stack objects
   *
   * @return object
   */
    public function get($stack = [])
    {
        /* FIX  */
         // Check if the result is already cached
         $cache_key = 'municipio_customizer_controller_vars';
         $cached_result = wp_cache_get($cache_key, 'municipio');
 
         if ($cached_result !== false && !isset($_GET['applicator'])) {
             return $cached_result;
         } 
        /* ENDFIX */ 

        //Get field definition
        $fields = \Kirki::$all_fields;

        //Determine what's a controller var, fetch it
        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $key => $field) {
                if ($this->isControllerSetting($field)) {
                    /**
                     * Implementation of output active_callback functionality for output.
                     * Can handle multiple AND statements.
                     */

                    if (!isset($field['active_callback'])) {
                        $stack = $this->appendStack($stack, \Kirki::get_option($key), $key, $field);
                    } elseif (is_string($field['active_callback']) && $field['active_callback'] === '__return_true') {
                        $stack = $this->appendStack($stack, \Kirki::get_option($key), $key, $field);
                    } elseif (is_string($field['active_callback']) && $field['active_callback'] === '__return_false') {
                        $stack = $this->appendStack($stack, null, $key, $field);
                    } elseif (is_array($field['active_callback']) && !empty($field['active_callback'])) {
                        $shouldReturn = false;

                        foreach ($field['active_callback'] as $cb) {
                            $cb = (object) $cb;

                            //Verify operator, before eval
                            if ($this->isValidOperator($cb->operator) === false) {
                                trigger_error("Provided operator in active callback for is not valid.", E_USER_ERROR);
                            }

                            //Verify value (sanity check)
                            if (!preg_match('/^[a-z\d_-]+$/i', $cb->value)) {
                                trigger_error("Provided value in active callback for is not valid, should be a string matching (a-z _ - digits).", E_USER_ERROR);
                            }

                            // Handle "contains" operator
                            if ('contains' == $cb->operator) {
                                $value = (array) \Kirki::get_option($cb->setting);
                                if (in_array($cb->value, $value)) {
                                    $shouldReturn = true;
                                }
                            } else {
                                //Create compare string
                                if (is_string($cb->value)) {
                                    $cb->value = '"' . $cb->value . '"';
                                }
                                if (eval('return \Kirki::get_option("' . $cb->setting . '") ' . $cb->operator . ' ' . $cb->value . ';')) {
                                    $shouldReturn = true;
                                }
                            }
                        }

                        if ($shouldReturn === true) {
                            $stack = $this->appendStack($stack, \Kirki::get_option($key), $key, $field);
                        } else {
                            $stack = $this->appendStack($stack, null, $key, $field);
                        }
                    }
                }
            }
        }

        /* FIX */ 

        // Camel case response keys, and return
        $result = \Municipio\Helper\FormatObject::camelCase(
            (object) $stack
        );

        // Cache the result for 12 hours
        wp_cache_set($cache_key, $result, 'municipio', 12 * HOUR_IN_SECONDS);

        /* ENDFIX */ 

        return $result;
    }

  /**
   * Validate PHP operator
   *
   * @param string $operator
   * @return bool
   */
    private function isValidOperator($operator): bool
    {
        if (in_array((string) $operator, ['contains', '==', '===', '!=', '<>', '!==', '>', '<', '>=', '<=', '<=>'])) {
            return true;
        }
        return false;
    }

  /**
   * Determines if should be handled as a controller var.
   *
   * @param array $field
   * @return boolean
   */
    private function isControllerSetting($field, $lookForType = 'controller')
    {
        if (isset($field['output']) && is_array($field['output']) && !empty($field['output'])) {
            foreach ($field['output'] as $output) {
                if (isset($output['type']) && $output['type'] === $lookForType) {
                    return true;
                }
            }
        }
        return false;
    }

   /**
   * Determine output type
   *
   * @param array $field
   * @return boolean
   */
    private function shouldStackInObject($field, $lookForType = 'controller')
    {
        if (isset($field['output']) && is_array($field['output']) && !empty($field['output'])) {
            foreach ($field['output'] as $output) {
                if (isset($output['type']) && $output['type'] === $lookForType) {
                    if (isset($output['as_object']) && $output['as_object'] === true) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Append to stack
     *
     * @param array $stack  Prevois stack object
     * @param string $value Value to append
     * @param string $key   Field key to store
     * @param array $field  Field definition
     * @return void
     */
    private function appendStack($stack, $value, $key, $field)
    {
        if ($this->shouldStackInObject($field)) {
            //Get and create object
            $section = $this->sanitizeStackObjectName($field['section']);
            if (!isset($stack[$section]) || !is_array($stack[$section])) {
                $stack[$section] = [];
            }

            //Store in sanitized item name
            $stack[$section][
                $this->sanitizeStackItemName($key, $section)
            ] = $value;
        } else {
            $stack[$key] = $value;
        }

        return $stack;
    }

    /**
     * Sanitize stack object name. Removes customizer panel prefix.
     *
     * @param string $name
     * @return string
     */
    private function sanitizeStackObjectName($name)
    {
        return str_replace('municipio_customizer_panel_', '', $name);
    }

    /**
     * Remove object name from item keys
     *
     * @param string $name
     * @param string $santizationString
     * @return string
     */
    private function sanitizeStackItemName($name, $santizationString)
    {
        return str_replace($santizationString, '', $name);
    }
}
