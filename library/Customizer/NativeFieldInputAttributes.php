<?php

namespace Municipio\Customizer;

class NativeFieldInputAttributes
{
    /**
     * Convert Kirki choice constraints to native input attributes.
     *
     * @param array $field Field configuration.
     *
     * @return array|null
     */
    public static function fromField(array $field): ?array
    {
        $choices = $field['choices'] ?? null;

        if (($field['type'] ?? '') !== 'slider' || !is_array($choices) || $choices === []) {
            return null;
        }

        return array_filter(
            [
                'min' => $choices['min'] ?? null,
                'max' => $choices['max'] ?? null,
                'step' => $choices['step'] ?? null,
            ],
            static fn($value): bool => $value !== null,
        );
    }
}
