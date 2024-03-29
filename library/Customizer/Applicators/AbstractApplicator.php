<?php

namespace Municipio\Customizer\Applicators;

abstract class AbstractApplicator
{
    private static $contextCache = [];

    /**
     * Determines if should be handled as a modifier.
     *
     * @param array $field
     * @param string $lookForType
     * @return boolean
     */
    protected function isFieldType($field, $lookForType): bool
    {
        if (!$this->fieldHasOutput($field)) {
            return false;
        }

        foreach ($field['output'] as $output) {
            if ($this->fieldOutputHasMatchingType($output, $lookForType)) {
                return true;
            }
        }

        return false;
    }

    private function fieldHasOutput(array $field): bool
    {
        if (!isset($field['output']) || !is_array($field['output'])) {
            return false;
        }

        return !empty($field['output']);
    }

    private function fieldOutputHasMatchingType(array $output, string $type): bool
    {
        if (!isset($output['type'])) {
            return false;
        }

        return $output['type'] === $type;
    }

    /**
     * Validate PHP operator
     *
     * @param string $operator
     * @return bool
     */
    protected function isValidOperator($operator): bool
    {
        $validOperators = ['==', '===', '!=', '<>', '!==', '>', '<', '>=', '<=', '<=>'];
        return in_array((string) $operator, $validOperators);
    }

    /**
     * Checks whether the field is hidden based on the active callbacks.
     */
    protected function activeCallbackHandler($field) {
        $conditional = [];
        if (!empty($field['active_callback']) && is_array($field['active_callback'])) {
            foreach ($field['active_callback'] as $callback) {
                $operator = $callback['operator'];
                if ($this->isValidOperator($operator)) {
                    if (!preg_match('/^[a-z\d_-]+$/i', $callback['setting']) || !preg_match('/^[a-z\d_-]+$/i', $callback['value'])) {
                        return;
                    }
                    $expression =  "\Kirki::get_option(\$callback['setting']) $operator \$callback['value'];";
                    $result = eval("return $expression;");
                    $conditional[] = $result;
                } else {
                    $conditional[] = true;
                }
            }
        } else { $conditional[] = true;}
        return in_array(false, $conditional);
    }

    protected function hasFilterContexts(array $contexts, array $filterContexts): bool
    {

        $cacheKey = md5(serialize($contexts) . serialize($filterContexts));

        if (isset(self::$contextCache[$cacheKey])) {
            return self::$contextCache[$cacheKey];
        }

        $hasContext = [];
        foreach ($filterContexts as $context) {
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
            $hasContext[] = (bool) eval('return in_array(' . $context['context'] . ', $contexts) ' . $context['operator'] . ' true;');
        }

        return (bool) self::$contextCache[$cacheKey] = array_product($hasContext);
    }
}
