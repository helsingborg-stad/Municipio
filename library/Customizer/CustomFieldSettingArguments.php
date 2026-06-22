<?php

namespace Municipio\Customizer;

class CustomFieldSettingArguments
{
    /**
     * Build native setting arguments for Municipio custom controls.
     *
     * @param array $field Field configuration.
     *
     * @return array
     */
    public static function fromField(array $field): array
    {
        return array_filter(
            [
                'type' => $field['option_type'] ?? 'theme_mod',
                'capability' => $field['capability'] ?? 'edit_theme_options',
                'default' => $field['default'] ?? self::getDefaultValue($field),
                'transport' => $field['transport'] ?? 'refresh',
                'sanitize_callback' => $field['sanitize_callback'] ?? self::getSanitizeCallback((string) ($field['type'] ?? 'text')),
            ],
            static fn($value): bool => $value !== null,
        );
    }

    /**
     * Resolve a default value for the field type.
     *
     * @param array $field Field configuration.
     *
     * @return mixed
     */
    private static function getDefaultValue(array $field): mixed
    {
        return match ($field['type'] ?? '') {
            'background', 'multicheck', 'multicolor', 'repeater', 'select' => [],
            default => '',
        };
    }

    /**
     * Resolve the sanitize callback for a field type.
     *
     * @param string $fieldType Field type.
     *
     * @return callable|string
     */
    private static function getSanitizeCallback(string $fieldType): callable|string
    {
        return match ($fieldType) {
            'background', 'multicheck', 'multicolor', 'repeater', 'select' => [self::class, 'sanitizeJsonArray'],
            'color' => 'sanitize_text_field',
            default => 'sanitize_text_field',
        };
    }

    /**
     * Sanitize a JSON encoded array or an array value recursively.
     *
     * @param mixed $value Submitted value.
     *
     * @return array
     */
    public static function sanitizeJsonArray(mixed $value): array
    {
        if (is_string($value)) {
            $decodedValue = json_decode(stripslashes($value), true);
            $value = is_array($decodedValue) ? $decodedValue : [];
        }

        if (!is_array($value)) {
            return [];
        }

        return self::sanitizeArray($value);
    }

    /**
     * Sanitize an array recursively.
     *
     * @param array $values Values to sanitize.
     *
     * @return array
     */
    private static function sanitizeArray(array $values): array
    {
        return array_map(static function (mixed $value): mixed {
            if (is_array($value)) {
                return self::sanitizeArray($value);
            }

            return trim(strip_tags((string) $value));
        }, $values);
    }
}
