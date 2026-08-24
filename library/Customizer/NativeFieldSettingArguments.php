<?php

namespace Municipio\Customizer;

class NativeFieldSettingArguments
{
    /**
     * Build native setting arguments from a Customizer-shaped field definition.
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
                'sanitize_callback' => $field['sanitize_callback'] ?? self::getSanitizeCallback($field),
            ],
            static fn($value): bool => $value !== null,
        );
    }

    /**
     * Resolve a native sanitize callback for the field type.
     *
     * @param array $field Field configuration.
     *
     * @return callable|string
     */
    private static function getSanitizeCallback(array $field): callable|string
    {
        $fieldType = (string) ($field['type'] ?? 'text');

        $sanitizeCallbacks = [
            'checkbox' => static fn($value): bool => (bool) $value,
            'checkbox_switch' => static fn($value): bool => (bool) $value,
            'code' => static fn($value): string => (string) $value,
            'color' => 'sanitize_hex_color',
            'hidden' => static fn($value): string => (string) $value,
            'switch' => static fn($value): bool => (bool) $value,
            'toggle' => static fn($value): bool => (bool) $value,
            'number' => 'absint',
            'textarea' => 'sanitize_textarea_field',
            'url' => 'esc_url_raw',
        ];

        if ($fieldType === 'slider') {
            return self::getSliderSanitizeCallback($field);
        }

        return $sanitizeCallbacks[$fieldType] ?? 'sanitize_text_field';
    }

    /**
     * Build a sanitize callback for slider fields that respects decimal min/max/step constraints.
     *
     * @param array $field Field configuration.
     *
     * @return callable
     */
    private static function getSliderSanitizeCallback(array $field): callable
    {
        $choices = is_array($field['choices'] ?? null) ? $field['choices'] : [];
        $min = (float) ($choices['min'] ?? 0);
        $max = (float) ($choices['max'] ?? $min);
        $step = (float) ($choices['step'] ?? 1);
        $precision = self::getStepPrecision($step);

        return static function (mixed $value) use ($min, $max, $step, $precision): int|float {
            $normalizedValue = (float) $value;
            $normalizedStep = $step > 0 ? $step : 1.0;
            $clampedValue = min(max($normalizedValue, $min), $max);
            $steppedValue = $min + round(($clampedValue - $min) / $normalizedStep) * $normalizedStep;
            $boundedValue = min(max($steppedValue, $min), $max);
            $roundedValue = round($boundedValue, $precision);

            return $precision === 0 ? (int) $roundedValue : $roundedValue;
        };
    }

    /**
     * Determine decimal precision from a slider step value.
     *
     * @param float $step Slider step value.
     *
     * @return int
     */
    private static function getStepPrecision(float $step): int
    {
        if ($step <= 0) {
            return 0;
        }

        $stepString = rtrim(rtrim(sprintf('%.10F', $step), '0'), '.');
        $decimalSeparatorPosition = strpos($stepString, '.');

        return $decimalSeparatorPosition === false ? 0 : strlen(substr($stepString, $decimalSeparatorPosition + 1));
    }
}
