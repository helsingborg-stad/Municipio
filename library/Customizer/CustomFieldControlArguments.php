<?php

namespace Municipio\Customizer;

class CustomFieldControlArguments
{
    /**
     * Build custom control arguments.
     *
     * @param array $field Field configuration.
     *
     * @return array
     */
    public static function fromField(array $field): array
    {
        $controlArguments = array_filter(
            [
                'section' => $field['section'] ?? '',
                'label' => $field['label'] ?? '',
                'description' => $field['description'] ?? '',
                'choices' => $field['choices'] ?? [],
                'input_attrs' => [
                    'fields' => $field['fields'] ?? [],
                ],
                'field' => $field,
            ],
            static fn($value): bool => $value !== null && $value !== '',
        );

        $activeCallback = NativeFieldActiveCallback::fromField($field['active_callback'] ?? null);

        if ($activeCallback !== null) {
            $controlArguments['active_callback'] = $activeCallback;
        }

        return $controlArguments;
    }
}
