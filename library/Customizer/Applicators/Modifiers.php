<?php

namespace Municipio\Customizer\Applicators;

use WP_Customize_Manager;

class Modifiers extends AbstractApplicator
{
    public $optionKey = 'modifiers';

    public function __construct()
    {
        add_action('customize_save_after', array($this, 'storeModifiers'), 50, 1);
        add_filter('ComponentLibrary/Component/Modifier', array($this, 'applyStoredModifiers'), 10, 2);
        add_action('Municipio/Customizer/Applicator/Modifiers/RefreshCache', array($this, 'storeModifiers'), 50, 1);
    }

    /**
     * Calculate and store modifiers on save of customizer
     *
     * @return void
     */
    public function storeModifiers($manager = null): array
    {
        if(is_a($manager, 'WP_Customize_Manager') || is_null($manager)) {
            $this->setStatic(
                $storedModifiers = $this->calculateModifiers(),
                $manager
            );
            return $storedModifiers;
        }
        return null;
    }

    /**
     * Apply stored modifiers
     *
     * @param array $modifiers
     * @param array $contexts
     * @return array
     */
    public function applyStoredModifiers($modifiers, $contexts)
    {
        if (!is_array($contexts)) {
            $contexts = [$contexts];
        }

        if (!is_array($modifiers)) {
            $modifiers = [$modifiers];
        }

        $storedModifiers = $this->getStatic();

        // If storedModifiers is empty, calculate and store them
        if ($storedModifiers === false) {
            $storedModifiers = $this->storeModifiers();
        }

        foreach ($storedModifiers as $filter) {
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

                if (
                    ($filterContext['operator'] == "==" && in_array($filterContext['context'], $contexts)) ||
                    ($filterContext['operator'] == "!=" && !in_array($filterContext['context'], $contexts))
                ) {
                    $passFilterRules = true;
                }
            }

            if ($passFilterRules) {
                $modifiers[] = $filter['value'];
            }
        }

        return $modifiers;
    }

    /**
     * Calculate modifiers based on fields
     *
     * @return array
     */
    private function calculateModifiers()
    {
        if ($runtimeCache = $this->getRuntimeCache('modifiersRuntimeCache')) {
            return $runtimeCache;
        }

        $fields    = $this->getFields();
        $modifiers = [];

        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $key => $field) {
                if (!$this->isFieldType($field, 'modifier')) {
                    continue;
                }

                if (!isset($field['active_callback']) || $this->isValidActiveCallback($field['active_callback'], $key)) {
                    if (!isset($field['output']) || !is_array($field['output'])) {
                        continue;
                    }

                    foreach ($field['output'] as $output) {
                        if (!isset($output['context']) || !is_array($output['context'])) {
                            continue;
                        }

                        foreach ($output['context'] as $contextKey => $context) {
                            if (!is_array($context)) {
                                $output['context'][$contextKey] = [
                                    'operator' => '==',
                                    'context'  => $context
                                ];
                            }
                        }

                        $modifiers[] = [
                            'contexts' => $output['context'],
                            'value'    => \Kirki::get_option($key),
                        ];
                    }
                }
            }
        }

        return $this->setRuntimeCache(
            'modifiersRuntimeCache',
            $modifiers
        );
    }
}
