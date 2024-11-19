<?php

namespace Municipio\Customizer\Applicators;

use Kirki\Compatibility\Kirki;
use Error;
use WP_Customize_Manager;

class ComponentData extends AbstractApplicator
{
    public $optionKey = 'component';

    public function __construct()
    {
        add_action('customize_save_after', array($this, 'storeComponentData'), 50);
        add_filter('ComponentLibrary/Component/Data', array($this, 'applyStoredComponentData'), 10);
        add_action('Municipio/Customizer/Applicator/ComponentData/RefreshCache', array($this, 'storeComponentData'), 50, 1);
    }

    /**
     * Calculate and store component data on save of customizer
     *
     * @return void
     */
    public function storeComponentData(?WP_Customize_Manager $manager = null)
    {
        $this->setStatic(
            $componentData = $this->calculateComponentData(),
            $manager
        );
        return $componentData;
    }

    /**
     * Apply stored component data
     *
     * @param array $data
     * @return array
     */
    public function applyStoredComponentData($data)
    {
        $storedComponentData = $this->getStatic();
        if ($storedComponentData === false) {
            $storedComponentData = $this->storeComponentData();
        }

        $contexts = isset($data['context']) ? (array) $data['context'] : [];

        foreach ($storedComponentData as $filter) {
            $passFilterRules = false;

            foreach ($filter['contexts'] as $filterContext) {
                // Operator and context must be set
                if (!isset($filterContext['operator']) || !isset($filterContext['context'])) {
                    throw new Error("Operator must be != or == to be used in ComponentData applicator. Context must be set. Provided values: " . print_r($filterContext, true));
                }

                // Operator must be != or ==
                if (!in_array($filterContext['operator'], ["!=", "=="])) {
                    throw new Error("Operator must be != or == to be used in ComponentData applicator. Provided value: " . $filterContext['operator']);
                }

                if (
                    ($filterContext['operator'] == "==" && in_array($filterContext['context'], $contexts)) ||
                    ($filterContext['operator'] == "!=" && !in_array($filterContext['context'], $contexts))
                ) {
                    $passFilterRules = true;
                }
            }

            if ($passFilterRules) {
                $data = array_replace_recursive($data, $filter['data']);
            }
        }

        return $data;
    }

    /**
     * Calculate component data based on fields
     *
     * @return array
     */
    private function calculateComponentData()
    {
        if ($runtimeCache = $this->getRuntimeCache('componentDataRuntimeCache')) {
            return $runtimeCache;
        }

        $fields        = $this->getFields();
        $componentData = [];

        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $key => $field) {
                if (!$this->isFieldType($field, 'component_data')) {
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

                        // Correct faulty context configurations
                        foreach ($output['context'] as $contextKey => $context) {
                            if (is_string($context)) {
                                $output['context'][$contextKey] = [
                                    'operator' => '==',
                                    'context'  => $context
                                ];
                            }
                        }

                        $filterData = $this->buildFilterData($output['dataKey'], \Kirki::get_option($key));

                        $componentData[] = [
                            'contexts' => is_array($output['context']) ? $output['context'] : [$output['context']],
                            'data'     => $filterData,
                        ];
                    }
                }
            }
        }

        return $this->setRuntimeCache(
            'componentDataRuntimeCache',
            $componentData
        );
    }

    /**
     * Build filter data from the given data key and value
     *
     * @param string $dataKey
     * @param mixed $value
     * @return array
     */
    public function buildFilterData(string $dataKey, $value): array
    {
        $filterData  = [];
        $previousArr = &$filterData;
        $fields      = explode('.', $dataKey);

        foreach ($fields as $i => $field) {
            if ($i === count($fields) - 1) {
                $previousArr[$field] = $value;
            } else {
                $previousArr[$field] = [];
                $previousArr         = &$previousArr[$field];
            }
        }

        return $filterData;
    }
}
