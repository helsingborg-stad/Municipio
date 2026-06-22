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
            'switch' => 'checkbox',
            'toggle' => 'checkbox',
            'slider' => 'number',
        ];

        return $typeMap[$fieldType] ?? $fieldType;
    }
}
