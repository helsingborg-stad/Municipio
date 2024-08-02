<?php

namespace Municipio\Customizer\Applicators;

class Modifiers extends AbstractApplicator
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
        // Get field definition
        $fields = $this->getFields();

        // Determine what's a controller var, fetch it
        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $key => $field) {
                if (!$this->isFieldType($field, 'modifier')) {
                    continue;
                }

                if (!isset($field['active_callback']) || $this->isValidActiveCallback($field['active_callback'], $key)) {
                    // Check if the field has an 'output' key
                    if (!isset($field['output']) || !is_array($field['output'])) {
                        continue;
                    }

                    foreach ($field['output'] as $output) {
                        // Check if the output has a 'context' key
                        if (!isset($output['context']) || !is_array($output['context'])) {
                            continue;
                        }

                        // Ensure context is correctly formatted. TODO: Correct faulty config.
                        foreach ($output['context'] as $contextKey => $context) {
                            if (!is_array($context)) {
                                $output['context'][$contextKey] = [
                                    'operator' => '==',
                                    'context' => $context
                                ];
                            }
                        }

                        // Prepare the filter for the add_filter call
                        $filter = [
                            'contexts' => $output['context'],
                            'value' => $key,
                        ];

                        add_filter('ComponentLibrary/Component/Modifier', function ($modifiers, $contexts) use ($filter) {
                            if (!is_array($contexts)) {
                                $contexts = [$contexts];
                            }

                            if (!is_array($modifiers)) {
                                $modifiers = [$modifiers];
                            }

                            $passFilterRules = false;

                            foreach ($filter['contexts'] as $filterContext) {
                                // Operator and context must be set
                                if (!isset($filterContext['operator']) || !isset($filterContext['context'])) {
                                    throw new \Error("Operator must be != or == to be used in ComponentData applicator. Context must be set. Provided values: " . print_r($filterContext, true));
                                }

                                // Operator must be != or ==
                                if (!in_array($filterContext['operator'], ["!=", "=="])) {
                                    throw new \Error("Operator must be != or == to be used in ComponentData applicator. Provided value: " . $filterContext['operator']);
                                }

                                if (($filterContext['operator'] == "==" && in_array($filterContext['context'], $contexts)) ||
                                    ($filterContext['operator'] == "!=" && !in_array($filterContext['context'], $contexts))) {
                                    $passFilterRules = true;
                                }
                            }

                            if ($passFilterRules) {
                                $modifiers[] =  \Kirki::get_option($filter['value']);
                            }

                            return $modifiers;
                        }, 10, 2);
                    }
                }
            }
        }
    }
}
