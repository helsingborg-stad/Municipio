<?php

namespace Municipio\Customizer;

use Kirki\Compatibility\Kirki;
use Municipio\Helper\KirkiConditional;
use Municipio\Customizer\PanelsRegistry;
use Municipio\Customizer;

class KirkiField
{
    private const NATIVE_FIELD_TYPES = [
        'checkbox' => 'checkbox',
        'color'    => 'color',
        'image'    => 'image',
        'number'   => 'number',
        'radio'    => 'radio',
        'select'   => 'select',
        'text'     => 'text',
        'textarea' => 'textarea',
        'upload'   => 'upload',
        'url'      => 'url',
    ];

    private const NATIVE_UNSUPPORTED_KEYS = [
        'active_callback',
        'alpha',
        'as_object',
        'multiple',
        'tab',
    ];

    private static array $nativeFields = [];
    private static bool $nativeFieldsHookAdded = false;

    public static function addField(array $field): void
    {
        PanelsRegistry::getInstance()->addRegisteredField($field);

        if (self::isNativeField($field)) {
            self::addNativeField($field);
            return;
        }

        Kirki::add_field(Customizer::KIRKI_CONFIG, $field);
    }

    public static function isNativeField(array $field): bool
    {
        if (
            !isset($field['settings'], $field['type'], self::NATIVE_FIELD_TYPES[$field['type']])
            || !is_string($field['settings'])
        ) {
            return false;
        }

        foreach (self::NATIVE_UNSUPPORTED_KEYS as $unsupportedKey) {
            if (!empty($field[$unsupportedKey])) {
                return false;
            }
        }

        return true;
    }

    public static function registerNativeFields(object $wpCustomize): void
    {
        foreach (self::$nativeFields as $field) {
            self::registerNativeField($wpCustomize, $field);
        }
    }

    public static function getNativeFields(): array
    {
        $fields = [];

        foreach (self::$nativeFields as $field) {
            $fields[$field['settings']] = $field;
        }

        return $fields;
    }

    public static function addProField($instance)
    {
        Kirki::add_field($instance);
    }

    public static function addConditionalField(array $fields, array $toggle)
    {
        if (self::isAssocArray($fields)) {
            $fields = [$fields];
        }

        foreach ($fields as $field) {
            PanelsRegistry::getInstance()->addRegisteredField($field);
        }

        KirkiConditional::add_field(Customizer::KIRKI_CONFIG, $fields, $toggle);
    }

    private static function isAssocArray($array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    private static function addNativeField(array $field): void
    {
        self::$nativeFields[] = $field;

        if (self::$nativeFieldsHookAdded) {
            return;
        }

        self::$nativeFieldsHookAdded = true;

        if (function_exists('add_action')) {
            add_action('customize_register', [self::class, 'registerNativeFields']);
        }
    }

    private static function registerNativeField(object $wpCustomize, array $field): void
    {
        $settingId = $field['settings'];

        $wpCustomize->add_setting($settingId, self::getNativeSettingArgs($field));
        self::addNativeControl($wpCustomize, $settingId, $field);
    }

    private static function getNativeSettingArgs(array $field): array
    {
        $args = [
            'default'           => $field['default'] ?? '',
            'type'              => 'theme_mod',
            'sanitize_callback' => self::getSanitizeCallback($field),
        ];

        foreach (['capability', 'transport'] as $key) {
            if (isset($field[$key])) {
                $args[$key] = $field[$key];
            }
        }

        if (isset($field['sanitize_callback'])) {
            $args['sanitize_callback'] = $field['sanitize_callback'];
        }

        return $args;
    }

    private static function addNativeControl(object $wpCustomize, string $settingId, array $field): void
    {
        $controlArgs = self::getNativeControlArgs($field);

        if ($field['type'] === 'color' && class_exists('WP_Customize_Color_Control')) {
            $wpCustomize->add_control(new \WP_Customize_Color_Control($wpCustomize, $settingId, $controlArgs));
            return;
        }

        if ($field['type'] === 'image' && class_exists('WP_Customize_Image_Control')) {
            $wpCustomize->add_control(new \WP_Customize_Image_Control($wpCustomize, $settingId, $controlArgs));
            return;
        }

        if ($field['type'] === 'upload' && class_exists('WP_Customize_Upload_Control')) {
            $wpCustomize->add_control(new \WP_Customize_Upload_Control($wpCustomize, $settingId, $controlArgs));
            return;
        }

        $wpCustomize->add_control($settingId, $controlArgs);
    }

    private static function getNativeControlArgs(array $field): array
    {
        $args = [
            'type'        => self::NATIVE_FIELD_TYPES[$field['type']],
            'label'       => $field['label'] ?? '',
            'description' => $field['description'] ?? '',
            'section'     => $field['section'] ?? '',
        ];

        foreach (['choices', 'priority'] as $key) {
            if (isset($field[$key])) {
                $args[$key] = $field[$key];
            }
        }

        return $args;
    }

    private static function getSanitizeCallback(array $field): callable
    {
        return match ($field['type']) {
            'checkbox' => fn($value): bool => (bool) $value,
            'number'   => fn($value) => is_numeric($value) ? $value : ($field['default'] ?? ''),
            'radio',
            'select'   => fn($value) => array_key_exists((string) $value, $field['choices'] ?? []) ? $value : ($field['default'] ?? ''),
            'textarea' => fn($value): string => function_exists('sanitize_textarea_field') ? sanitize_textarea_field($value) : strip_tags((string) $value),
            'url',
            'image',
            'upload'   => fn($value): string => function_exists('esc_url_raw') ? esc_url_raw($value) : filter_var((string) $value, FILTER_SANITIZE_URL),
            default    => fn($value): string => function_exists('sanitize_text_field') ? sanitize_text_field($value) : strip_tags((string) $value),
        };
    }
}
