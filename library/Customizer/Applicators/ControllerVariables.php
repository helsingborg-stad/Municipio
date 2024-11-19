<?php

namespace Municipio\Customizer\Applicators;

class ControllerVariables extends AbstractApplicator
{
    public $optionKey = 'controller';

    public function __construct()
    {
        add_filter('Municipio/Controller/Customizer', array($this, 'applicateStoredControllerVars'));
        add_action('customize_save_after', array($this, 'storeControllerVars'), 50, 1);
        add_action('Municipio/Customizer/Applicator/ControllerVars/RefreshCache', array($this, 'storeControllerVars'), 50, 1);
    }

    /**
     * Calculate controller vars on save of customizer
     *
     * @return array
     */
    public function storeControllerVars($manager = null)
    {
        $this->setStatic(
            $controllerVars = $this->get()
        );
        return $controllerVars;
    }

    /**
     * Populate controller vars from stored data, if available.
     * Otherwise, calculate, store and try again.
     *
     * @return array
     */
    public function applicateStoredControllerVars()
    {
        if ($controllerVars = $this->getStatic()) {
            return $controllerVars;
        }
        return $this->storeControllerVars(); // Fallback to calculate and store
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
        if ($runtimeCache = $this->getRuntimeCache('controllerVarsRuntimeCache')) {
            return $runtimeCache;
        }

        //Get field definition
        $fields = $this->getFields();

        //Determine what's a controller var, fetch it
        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $key => $field) {
                // Check if field is a controller
                if (!$this->isFieldType($field, 'controller')) {
                    continue;
                }

                if (!isset($field['active_callback']) || $this->isValidActiveCallback($field['active_callback'], $key)) {
                    $stack = $this->appendStack(
                        $stack,
                        \Kirki::get_option($key),
                        $key,
                        $field
                    );
                } else {
                    $stack = $this->appendStack(
                        $stack,
                        null,
                        $key,
                        $field
                    );
                }
            }
        }
        // Camel case response keys, and return
        return $this->setRuntimeCache(
            'controllerVarsRuntimeCache',
            \Municipio\Helper\FormatObject::camelCase(
                (object) $stack
            )
        );
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
