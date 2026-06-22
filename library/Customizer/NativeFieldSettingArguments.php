<?php

namespace Municipio\Customizer;

class NativeFieldSettingArguments
{
    /**
     * Build native setting arguments from a Kirki-shaped field definition.
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
                'default' => $field['default'] ?? '',
                'transport' => $field['transport'] ?? 'refresh',
                'sanitize_callback' => $field['sanitize_callback'] ?? self::getSanitizeCallback((string) ($field['type'] ?? 'text')),
            ],
            static fn($value): bool => $value !== null,
        );
    }

    /**
     * Resolve a native sanitize callback for the field type.
     *
     * @param string $fieldType Field type.
     *
     * @return callable|string
     */
    private static function getSanitizeCallback(string $fieldType): callable|string
    {
        $sanitizeCallbacks = [
            'checkbox' => static fn($value): bool => (bool) $value,
            'switch' => static fn($value): bool => (bool) $value,
            'toggle' => static fn($value): bool => (bool) $value,
            'number' => 'absint',
            'slider' => 'absint',
            'textarea' => 'sanitize_textarea_field',
            'url' => 'esc_url_raw',
        ];

        return $sanitizeCallbacks[$fieldType] ?? 'sanitize_text_field';
    }
}
