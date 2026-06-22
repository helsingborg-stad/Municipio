<?php

namespace Municipio\Customizer;

class NativeFieldControlArguments
{
    /**
     * Build native control arguments from a Kirki-shaped field definition.
     *
     * @param array $field Field configuration.
     *
     * @return array
     */
    public static function fromField(array $field): array
    {
        $controlArguments = array_filter(
            [
                'type' => self::getNativeControlType((string) ($field['type'] ?? 'text')),
                'section' => $field['section'] ?? '',
                'label' => $field['label'] ?? '',
                'description' => $field['description'] ?? '',
                'choices' => $field['choices'] ?? null,
                'input_attrs' => NativeFieldInputAttributes::fromField($field),
                'code_type' => self::getCodeType($field),
            ],
            static fn($value): bool => $value !== null && $value !== '',
        );

        $activeCallback = NativeFieldActiveCallback::fromField($field['active_callback'] ?? null);

        if ($activeCallback !== null) {
            $controlArguments['active_callback'] = $activeCallback;
        }

        return $controlArguments;
    }

    /**
     * Map Kirki field types to native WordPress Customizer control types.
     *
     * @param string $fieldType Field type.
     *
     * @return string
     */
    private static function getNativeControlType(string $fieldType): string
    {
        $typeMap = [
            'checkbox_switch' => 'checkbox',
            'radio_buttonset' => 'radio',
            'switch' => 'checkbox',
            'toggle' => 'checkbox',
            'slider' => 'number',
        ];

        return $typeMap[$fieldType] ?? $fieldType;
    }

    /**
     * Get the code editor content type for code controls.
     *
     * @param array $field Field configuration.
     *
     * @return string|null
     */
    private static function getCodeType(array $field): ?string
    {
        if (($field['type'] ?? '') !== 'code') {
            return null;
        }

        $language = $field['choices']['language'] ?? 'text';

        return match ($language) {
            'css' => 'text/css',
            'html' => 'text/html',
            'js', 'javascript' => 'application/javascript',
            'json' => 'application/json',
            default => 'text/plain',
        };
    }
}
