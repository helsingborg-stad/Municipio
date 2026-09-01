<?php

namespace Municipio\Customizer;

use WP_Customize_Manager;

class NativeField
{
    public const FIELD_DRIVER = 'native';
    public const FIELD_DRIVER_KEY = 'municipio_customizer_field_driver';

    /**
     * Add a native WordPress Customizer field while keeping applicator metadata available.
     *
     * @param array $field Field configuration using the same shape as CustomizerField.
     *
     * @return void
     */
    public static function addField(array $field): void
    {
        if (!self::supports($field)) {
            return;
        }

        $field = self::withApplicatorMetadata($field);

        PanelsRegistry::getInstance()->addRegisteredField($field);

        add_action('customize_register', static function (WP_Customize_Manager $wpCustomize) use ($field): void {
            self::registerField($wpCustomize, $field);
        });
    }

    /**
     * Register the native setting and control with WordPress.
     *
     * @param WP_Customize_Manager $wpCustomize WordPress Customizer manager.
     * @param array                $field       Field configuration.
     *
     * @return void
     */
    public static function registerField(WP_Customize_Manager $wpCustomize, array $field): void
    {
        $settingId = self::getSettingId($field);

        if ($settingId === '') {
            return;
        }

        $wpCustomize->add_setting($settingId, NativeFieldSettingArguments::fromField($field));
        NativeFieldControlFactory::addControl($wpCustomize, $settingId, $field);
    }

    /**
     * Determine if the field can be represented by native WordPress Customizer controls.
     *
     * @param array $field Field configuration.
     *
     * @return bool
     */
    public static function supports(array $field): bool
    {
        return NativeFieldSupport::supports($field);
    }

    /**
     * Build native setting arguments from a Customizer-shaped field definition.
     *
     * @param array $field Field configuration.
     *
     * @return array
     */
    public static function getSettingArguments(array $field): array
    {
        return NativeFieldSettingArguments::fromField($field);
    }

    /**
     * Build native control arguments from a Customizer-shaped field definition.
     *
     * @param array $field Field configuration.
     *
     * @return array
     */
    public static function getControlArguments(array $field): array
    {
        return NativeFieldControlArguments::fromField($field);
    }

    /**
     * Add metadata used by customizer applicators.
     *
     * @param array $field Field configuration.
     *
     * @return array
     */
    private static function withApplicatorMetadata(array $field): array
    {
        $field[self::FIELD_DRIVER_KEY] = self::FIELD_DRIVER;

        return $field;
    }

    /**
     * Get the field setting identifier.
     *
     * @param array $field Field configuration.
     *
     * @return string
     */
    private static function getSettingId(array $field): string
    {
        return is_string($field['settings'] ?? null) ? $field['settings'] : '';
    }
}

class NativeFieldSupport
{
    private const NATIVE_FIELD_TYPES = [
        'checkbox',
        'checkbox_switch',
        'code',
        'color',
        'email',
        'hidden',
        'image',
        'number',
        'radio',
        'radio_buttonset',
        'select',
        'slider',
        'switch',
        'text',
        'textarea',
        'toggle',
        'upload',
        'url',
    ];

    /**
     * Determine if the field can be represented by native WordPress Customizer controls.
     *
     * @param array $field Field configuration.
     *
     * @return bool
     */
    public static function supports(array $field): bool
    {
        $type = $field['type'] ?? null;

        if (!is_string($type) || !in_array($type, self::NATIVE_FIELD_TYPES, true)) {
            return false;
        }

        return !self::isMultipleSelect($field) && !self::isAlphaColor($field);
    }

    /**
     * Determine if a select field depends on multi-select behavior.
     *
     * @param array $field Field configuration.
     *
     * @return bool
     */
    private static function isMultipleSelect(array $field): bool
    {
        return ($field['type'] ?? null) === 'select' && isset($field['multiple']) && $field['multiple'] !== false && $field['multiple'] !== 0 && $field['multiple'] !== '0';
    }

    /**
     * Determine if a color field depends on Customizer alpha/rgba behavior.
     *
     * @param array $field Field configuration.
     *
     * @return bool
     */
    private static function isAlphaColor(array $field): bool
    {
        if (($field['type'] ?? null) !== 'color') {
            return false;
        }

        if (($field['alpha'] ?? false) === true || ($field['choices']['alpha'] ?? false) === true) {
            return true;
        }

        return is_string($field['default'] ?? null) && str_starts_with($field['default'], 'rgba(');
    }
}
