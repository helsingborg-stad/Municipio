<?php

namespace Municipio\Helper\EnsureArrayOf;

/**
 * Helper class to ensure a value is an array of a specific object type.
 */
class EnsureArrayOf
{
    /**
     * Ensures that a value is an array of a specific type.
     *
     * @param mixed $value The value to check.
     * @param class-string<T> $ensuredType The class name that the items in the array must be instances of.
     * @template T
     * @return T[] The filtered array of items that are instances of the ensured type.
     */
    public static function ensureArrayOf(mixed $value, string $ensuredType): array
    {
        return array_values(
            array_filter(
                is_array($value) ? $value : [$value],
                fn($item) => is_object($item) && is_a($item, $ensuredType)
            )
        );
    }
}
