<?php

namespace Municipio\Customizer\Applicators;

use Kirki\Compatibility\Kirki;

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
                                $contexts = is_string($data['context']) ? [$data['context']] : $data['context'];

                                //Component has no context, do not apply filter
                                if(!is_array($contexts) || empty($data['context'])) {
                                    return $data;
                                }

                                foreach($filter['contexts'] as $filterContext) {

                                    // Operator and context must be set
                                    if(!isset($filterContext['operator']) || !isset($filterContext['context'])) {
                                        continue;
                                    }

                                    // Operator must be != or ==
                                    if(!in_array($filterContext['operator'], ["!=","=="])) {
                                        continue;
                                    }

                                    // Operator ==
                                    if (isset($filterContext['context']) && in_array($filterContext['context'], $contexts)) {
                                        $data = array_replace_recursive(
                                            $data, 
                                            $filter['data']
                                        );
                                        break;
                                    }

                                    // Operator !=
                                    if ($filterContext['operator'] == "==" && !in_array($filterContext['context'], $contexts)) {
                                        $data = array_replace_recursive(
                                            $data, 
                                            $filter['data']
                                        );
                                        break;
                                    }

                                    if ($filterContext['operator'] == "!=" && !in_array($filterContext['context'], $contexts)) {
                                        $data = array_replace_recursive(
                                            $data, 
                                            $filter['data']
                                        );
                                        break;
                                    }
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
