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
                        $stack[$key] = \Kirki::get_option($key);
                    } elseif (is_string($field['active_callback']) && $field['active_callback'] === '__return_true') {
                        $stack[$key] = \Kirki::get_option($key);
                    } elseif (is_string($field['active_callback']) && $field['active_callback'] === '__return_false') {
                        $stack[$key] = null;
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

                            //Create compare string
                            if (is_string($cb->value)) {
                                $cb->value = '"' . $cb->value . '"';
                            }

                            if (eval('return \Kirki::get_option("' . $cb->setting . '") ' . $cb->operator . ' ' . $cb->value . ';')) {
                                $shouldReturn = true;
                            }
                        }

                        if ($shouldReturn === true) {
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

  /**
   * Validate PHP operator
   *
   * @param string $operator
   * @return bool
   */
    private function isValidOperator($operator): bool
    {
        if (in_array((string) $operator, ['==', '===', '!=', '<>', '!==', '>', '<', '>=', '<=', '<=>'])) {
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
}
