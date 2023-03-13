<?php

namespace Municipio\Customizer\Applicators;

class ComponentData extends AbstractApplicator
{
    public function __construct()
    {
        add_action('wp', array($this, 'applyComponentData'));
    }

    /**
     * Apply component data
     *
     * @return void
     */
    public function applyComponentData()
    {
        //Get field definition
        $fields = \Kirki::$all_fields;

        //Determine what's a component var, fetch it
        if (is_array($fields) && !empty($fields)) {
            foreach ($fields as $key => $field) {
                if (!$this->isFieldType($field, 'component_data')) {
                    continue;
                }

                

                if (isset($field['output']) && is_array($field['output']) &&  !empty($field['output'])) {
                    foreach ($field['output'] as $output) {
                        
                        if (isset($output['context'])) {
                            $filterData = $this->buildFilterData(
                                $output['dataKey'], 
                                \Kirki::get_option($key)
                            );

                            
                            $filter = [
                                'contexts'  => $output['context'],
                                'data'      => $filterData
                            ];
                        }
                    }
                }

                add_filter('ComponentLibrary/Component/Data', function ($data) use ($filter) {
                    $contexts = is_string($data['context']) ? [$data['context']] : $data['context'];

                    


                    if (is_array($contexts) && !empty($contexts)) {
                        foreach ($contexts as $context) {


                            if (in_array($context, $filter['contexts'])) {
                                $data = array_replace_recursive($data, $filter['data']);
                                break;
                            }
                        }
                        // Check if contexts filter is multidimensional (new format)
                        if (count($filter['contexts']) !== count($filter['contexts'], COUNT_RECURSIVE)) {
                            if ($this->hasFilterContexts($contexts, $filter['contexts'])) {
                                $data = array_replace_recursive($data, $filter['data']);
                            }
                        }
                    }

                    return $data;
                }, 10, 2);
            }
        }
    }

    /**
     * @param string $dataKey
     * @param mixed $value
     * 
     * @return array Component data array
     */
    private function buildFilterData(string $dataKey, $value): array
    {
        $filterData = [];
        $previousArr = &$filterData;
        $fields = explode('.', $dataKey);
        for ($i = 0; $i < count($fields); $i++) {
            if ($i === count($fields) - 1) {
                $previousArr[$fields[$i]] = $value;
                break;
            }
            $previousArr[$fields[$i]] = [];
            $previousArr = &$previousArr[$fields[$i]];
        }
        return $filterData;
    }
}
