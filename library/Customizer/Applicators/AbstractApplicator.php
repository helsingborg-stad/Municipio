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
        if( !$this->fieldHasOutput($field) ) {
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
        if (!isset($field['output'])) return false;
        if (!is_array($field['output'])) return false;
        if (empty($field['output'])) return false;
        
        return true;
    }
    
    private function fieldOutputHasMatchingType(array $output, string $type): bool
    {
        if (!isset($output['type'])) return false;
        if ($output['type'] !== $type) return false;
        
        return true;
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
