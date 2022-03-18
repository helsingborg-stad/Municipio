<?php

namespace Municipio\Customizer\Applicators;

abstract class AbstractApplicator
{
    /**
     * Determines if should be handled as a modifier.
     *
     * @param array $field
     * @param string $lookForType
     * @return boolean
     */
    protected function isFieldType($field, $lookForType)
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
    protected function isValidOperator($operator): bool
    {
        if (in_array((string) $operator, ['==', '===', '!=', '<>', '!==', '>', '<', '>=', '<=', '<=>'])) {
            return true;
        }
        return false;
    }

    protected function hasFilterContexts(array $contexts, array $filterContexts): bool
    {
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

        return (bool) array_product($hasContext);
    }
}
