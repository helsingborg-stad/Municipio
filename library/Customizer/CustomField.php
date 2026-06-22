<?php

namespace Municipio\Customizer;

use Municipio\Customizer\CustomFieldControlFactory as ControlFactory;
use Municipio\Customizer\CustomFieldSettingArguments as SettingArguments;
use WP_Customize_Manager;

class CustomField
{
    public const FIELD_DRIVER = 'custom';
    public const FIELD_DRIVER_KEY = 'municipio_customizer_field_driver';

    /**
     * Add a Municipio custom Customizer field while keeping applicator metadata available.
     *
     * @param array $field Field configuration using the same shape as KirkiField.
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
     * Register the custom setting and control with WordPress.
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

        $wpCustomize->add_setting($settingId, SettingArguments::fromField($field));
        ControlFactory::addControl($wpCustomize, $settingId, $field);
    }

    /**
     * Determine if the field should be represented by Municipio custom controls.
     *
     * @param array $field Field configuration.
     *
     * @return bool
     */
    public static function supports(array $field): bool
    {
        return CustomFieldSupport::supports($field);
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

class CustomFieldSupport
{
    private const CUSTOM_FIELD_TYPES = [
        'background',
        'color',
        'custom',
        'divider',
        'headline',
        'multicheck',
        'multicolor',
        'repeater',
        'select',
    ];

    /**
     * Determine if the field should be represented by Municipio custom controls.
     *
     * @param array $field Field configuration.
     *
     * @return bool
     */
    public static function supports(array $field): bool
    {
        $type = $field['type'] ?? null;

        if (!is_string($type) || !in_array($type, self::CUSTOM_FIELD_TYPES, true)) {
            return false;
        }

        return match ($type) {
            'color' => self::isAlphaColor($field),
            'select' => self::isMultipleSelect($field),
            default => true,
        };
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
        return ($field['multiple'] ?? false) === true;
    }

    /**
     * Determine if a color field depends on alpha/rgba behavior.
     *
     * @param array $field Field configuration.
     *
     * @return bool
     */
    private static function isAlphaColor(array $field): bool
    {
        if (($field['alpha'] ?? false) === true || ($field['choices']['alpha'] ?? false) === true) {
            return true;
        }

        return is_string($field['default'] ?? null) && str_starts_with($field['default'], 'rgba(');
    }
}
