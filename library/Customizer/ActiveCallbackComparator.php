<?php

namespace Municipio\Customizer;

class ActiveCallbackComparator
{
    /**
     * Compare two values using Customizer active-callback operators.
     *
     * @param mixed  $storedValue Stored setting value.
     * @param mixed  $expectedValue Expected condition value.
     * @param string $operator Comparison operator.
     *
     * @return bool
     */
    public static function compare(mixed $storedValue, mixed $expectedValue, string $operator): bool
    {
        return match ($operator) {
            '==', '=' => $storedValue == $expectedValue,
            '===', 'equals' => $storedValue === $expectedValue,
            '!=', '<>' => $storedValue != $expectedValue,
            '!==', 'not equals' => $storedValue !== $expectedValue,
            '>', 'greater than' => $storedValue > $expectedValue,
            '>=', 'greater or equal' => $storedValue >= $expectedValue,
            '<', 'less than' => $storedValue < $expectedValue,
            '<=', 'less or equal' => $storedValue <= $expectedValue,
            'contains', 'in' => is_array($storedValue) && in_array($expectedValue, $storedValue, true),
            'does not contain', 'not in' => is_array($storedValue) && !in_array($expectedValue, $storedValue, true),
            default => $storedValue == $expectedValue,
        };
    }
}
