<?php

namespace Municipio\Helper\EnsureArrayOf;

/**
 * Helper class to ensure a value is an array of a specific type.
 */
class EnsureArrayOf
{
    private const SCALAR_TYPES = ['int', 'integer', 'float', 'double', 'string', 'bool', 'boolean', 'null'];

    /**
     * Ensures that a value is an array of a specific type.
     *
     * @param mixed $value The value to check.
     * @param class-string<T>|string $ensuredType The type name (e.g. 'string', 'int', 'bool') or class name.
     * @template T
     * @return T[] The filtered array of items matching the ensured type.
     */
    public static function ensureArrayOf(mixed $value, string $ensuredType): array
    {
        $items = is_array($value) ? $value : [$value];

        if (in_array(strtolower($ensuredType), self::SCALAR_TYPES, true)) {
            return array_values(
                array_filter($items, fn($item) => gettype($item) === self::normalizeType($ensuredType)),
            );
        }

        return array_values(
            array_filter($items, fn($item) => is_object($item) && is_a($item, $ensuredType)),
        );
    }

    private static function normalizeType(string $type): string
    {
        return match (strtolower($type)) {
            'int', 'integer' => 'integer',
            'float', 'double' => 'double',
            'bool', 'boolean' => 'boolean',
            'null' => 'NULL',
            default => $type,
        };
    }
}
