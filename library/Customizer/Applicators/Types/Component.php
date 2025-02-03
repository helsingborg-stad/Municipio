<?php

namespace Municipio\Customizer\Applicators\Types;

use Municipio\Customizer\Applicators\AbstractApplicator;
use Municipio\Customizer\Applicators\ApplicatorInterface;
use WpService\WpService;
use Error;

class Component extends AbstractApplicator implements ApplicatorInterface
{
    private array $cachedData = [];

    public function __construct(private WpService $wpService)
    {
    }

    public function getKey(): string
    {
        return 'component';
    }

    public function applyData(array|object $data)
    {
        $this->cachedData = $data;
        $this->wpService->addFilter('ComponentLibrary/Component/Data', [$this, 'applyDataFilterFunction'], 10, 1);
    }

    /**
     * Apply data filter function to the provided data.
     *
     * @param array $data The data to apply the filter on.
     * @return array The filtered data.
     * @throws Error If the operator or context is not set correctly.
     */
    public function applyDataFilterFunction($data)
    {
        $storedComponentData = $this->cachedData;

        $contexts = isset($data['context']) ? (array) $data['context'] : [];

        foreach ($storedComponentData as $filter) {
            $passFilterRules = false;

            $andOperators = array_filter($filter['contexts'], function ($context) {
                return in_array($context['operator'], ['===', '!=='], true);
            });

            foreach ($filter['contexts'] as $filterContext) {
                // Operator and context must be set
                if (!isset($filterContext['operator']) || !isset($filterContext['context'])) {
                    throw new Error("Operator must be !=, !== or ==, === to be used in ComponentData applicator. Context must be set. Provided values: " . print_r($filterContext, true));
                }

                // Operator must be != or ==
                if (!in_array($filterContext['operator'], ["!=", "==", "!==", "==="])) {
                    throw new Error("Operator must be !=, !== or ==, === to be used in ComponentData applicator. Provided value: " . $filterContext['operator']);
                }

                if (
                    (($filterContext['operator'] == "==" && in_array($filterContext['context'], $contexts)) ||
                    ($filterContext['operator'] == "!=" && !in_array($filterContext['context'], $contexts))) &&
                    $this->checkAndOperators($andOperators, $contexts)
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
     * Checks if all the given contexts satisfy the specified operators.
     *
     * @param array $andOperators An array of and operators, each containing a 'context' and 'operator'.
     * @param array $contexts An array of contexts to check against.
     * @return bool Returns true if all the contexts satisfy the operators, false otherwise.
     */
    private function checkAndOperators(array $andOperators, array $contexts)
    {
        foreach ($andOperators as $andOperator) {
            $context  = $andOperator['context'];
            $operator = $andOperator['operator'];

            if (
                ($operator == "===" && !in_array($context, $contexts)) ||
                ($operator == "!==" && in_array($context, $contexts))
            ) {
                return false;
            }
        }

        return true;
    }

    public function getData(): array
    {
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

                        foreach ($output['context'] as $contextKey => $context) {
                            if (is_string($context)) {
                                $output['context'][$contextKey] = [
                                'operator' => '==',
                                'context'  => $context
                                ];
                            }
                        }

                        $filterData = $this->buildFilterData(
                            $output['dataKey'],
                            \Kirki::get_option($key)
                        );

                        $componentData[] = [
                          'contexts' => is_array($output['context']) ? $output['context'] : [$output['context']],
                          'data'     => $filterData,
                        ];
                    }
                }
            }
        }

        return $componentData;
    }

  /**
   * Build filter data from the given data key and value
   *
   * @param string $dataKey
   * @param mixed $value
   * @return array
   */
    private function buildFilterData(string $dataKey, $value): array
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
