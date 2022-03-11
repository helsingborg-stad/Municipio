<?php

namespace Municipio\Customizer\Applicators;

class Modifiers
{
    public function __construct()
    {
        add_action('wp', array($this, 'applyModifiers'));
    }

    /**
    * Apply modifiers
    *
    * @return void
    */
    public function applyModifiers()
    {

        //Get field definition
        $fields = \Kirki::$all_fields;

        //Determine what's a controller var, fetch it
        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $key => $field) {
                if (!$this->isModifierSetting($field)) {
                    continue;
                }

                if (isset($field['output']) && is_array($field['output']) &&  !empty($field['output'])) {
                    foreach ($field['output'] as $output) {
                        if (isset($output['context'])) {
                            $filter = [
                                'contexts'  => $output['context'],
                                'value'     => \Kirki::get_option($key)
                            ];
                        }
                    }
                }

                add_filter('ComponentLibrary/Component/Modifier', function ($modifiers, $contexts) use ($filter) {

                    if (!is_array($contexts)) {
                        $contexts = [$contexts];
                    }

                    if (!is_array($modifiers)) {
                        $modifiers = [$modifiers];
                    }

                    if (is_array($contexts) && !empty($contexts)) {
                        foreach ($contexts as $context) {
                            if (in_array($context, $filter['contexts'])) {
                                $modifiers[] = $filter['value'];
                            }
                        }
                    }

                    return $modifiers;
                }, 10, 2);
            }
        }
    }

  /**
   * Determines if should be handled as a modifier.
   *
   * @param array $field
   * @return boolean
   */
    private function isModifierSetting($field, $lookForType = 'modifier')
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
