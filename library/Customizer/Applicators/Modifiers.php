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
                                break;
                            }
                        }
                        // Check if contexts filter is multidimensional (new format)
                        if (count($filter['contexts']) !== count($filter['contexts'], COUNT_RECURSIVE)) {
                            $applyModifier = [];
                            foreach ($filter['contexts'] as $context) {
                                //Verify operator, before eval
                                if ($this->isValidOperator($context['operator']) === false) {
                                    trigger_error("Provided operator in context for modifier is not valid.", E_USER_ERROR);
                                }

                                // Verify that context is a string and format it for eval
                                if (is_string($context['context'])) {
                                    $context['context'] = '"' . $context['context'] . '"';
                                } else {
                                    trigger_error("Provided context value in context for modifier is not a string.", E_USER_ERROR);
                                }

                                // Check if provided context exists in context array and compare using operator
                                $applyModifier[] = (bool) eval('return in_array(' . $context['context'] . ', $contexts) ' . $context['operator'] . ' true;');
                            }
                            if ((bool) array_product($applyModifier)) {
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
}
