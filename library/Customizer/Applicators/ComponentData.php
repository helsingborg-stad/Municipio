<?php

namespace Municipio\Customizer\Applicators;

use Kirki\Compatibility\Kirki;
use Error;

class ComponentData extends AbstractApplicator
{
    public function __construct()
    {
        add_action('wp', array($this, 'handleFields'));
    }

    /**
     * Apply component data
     *
     * @return void
     */
    public function handleFields($fields)
    {
        $fields = $this->getFields(); 

        // Determine what's a controller var, fetch it
        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $key => $field) {

                // Check if field is a controller
                if (!$this->isFieldType($field, 'component_data')) {
                    continue;
                }

                if (!isset($field['active_callback']) || $this->isValidActiveCallback($field['active_callback'], $key)) {
                    foreach ($field['output'] as $output) {
                        if (isset($output['context'])) {
                            $filterData = $this->buildFilterData(
                                $output['dataKey'], 
                                \Kirki::get_option($key)
                            );

                            $filter = [
                                'contexts' => is_array($output['context']) ? $output['context'] : [$output['context']],
                                'data' => $filterData,
                            ];

                            // Add the filter here
                            add_filter('ComponentLibrary/Component/Data', function ($data) use ($filter) {

                                // Normalize context
                                $contexts = !is_array($data['context']) ? [$data['context']] : $data['context'];

                                //Component has no context, do not apply filter
                                if(empty($data['context'])) {
                                    return $data;
                                }

                                $passFilterRules = false;

                                foreach($filter['contexts'] as $filterContext) {

                                    //Catch bad data. TODO: Fix bad data in field config.
                                    if(is_string($filterContext)) {
                                        $filterContext = [
                                            'operator' => '==',
                                            'context' => $filterContext
                                        ];
                                    }

                                    // Operator and context must be set
                                    if(!isset($filterContext['operator']) || !isset($filterContext['context'])) {
                                        throw new Error("Operator must be != or == to be used in ComponentData applicator. Context must be set. Provided values: " . print_r($filterContext, true));
                                    }

                                    // Operator must be != or ==
                                    if(!in_array($filterContext['operator'], ["!=","=="])) {
                                        throw new Error("Operator must be != or == to be used in ComponentData applicator. Provided value: " . $filterContext['operator']);
                                    }

                                    if (($filterContext['operator'] == "==" && in_array($filterContext['context'], $contexts)) ||
                                        ($filterContext['operator'] == "!=" && !in_array($filterContext['context'], $contexts))) {
                                        $passFilterRules = true;
                                    }
                                }

                                if($passFilterRules) {
                                    $data = array_replace_recursive(
                                        $data, 
                                        $filter['data']
                                    );
                                }

                                return $data;
                            }, 10, 2);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $dataKey
     * @param mixed $value
     * 
     * @return array Component data array
     */
    public function buildFilterData(string $dataKey, $value): array
    {
        $filterData = [];
        $previousArr = &$filterData;
        $fields = explode('.', $dataKey);

        foreach ($fields as $i => $field) {
            if ($i === count($fields) - 1) {
                $previousArr[$field] = $value;
            } else {
                $previousArr[$field] = [];
                $previousArr = &$previousArr[$field];
            }
        }

        return $filterData;
    }

}
