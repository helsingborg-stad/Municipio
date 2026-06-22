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
     * @param array $field Field configuration using the same shape as KirkiField.
     *
     * @return void
     */
    public static function addField(array $field): void
    {
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
        $wpCustomize->add_control($settingId, NativeFieldControlArguments::fromField($field));
    }

    /**
     * Build native setting arguments from a Kirki-shaped field definition.
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
     * Build native control arguments from a Kirki-shaped field definition.
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
