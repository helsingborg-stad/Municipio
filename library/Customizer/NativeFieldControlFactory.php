<?php

namespace Municipio\Customizer;

use WP_Customize_Manager;

class NativeFieldControlFactory
{
    /**
     * Add a native control for a field.
     *
     * @param WP_Customize_Manager $wpCustomize WordPress Customizer manager.
     * @param string               $settingId   Setting identifier.
     * @param array                $field       Field configuration.
     *
     * @return void
     */
    public static function addControl(WP_Customize_Manager $wpCustomize, string $settingId, array $field): void
    {
        $controlArguments = NativeFieldControlArguments::fromField($field);

        if (self::addSpecializedControl($wpCustomize, $settingId, $field, $controlArguments)) {
            return;
        }

        $wpCustomize->add_control($settingId, $controlArguments);
    }

    /**
     * Add a native specialized control when WordPress provides one.
     *
     * @param WP_Customize_Manager $wpCustomize      WordPress Customizer manager.
     * @param string               $settingId        Setting identifier.
     * @param array                $field            Field configuration.
     * @param array                $controlArguments Native control arguments.
     *
     * @return bool
     */
    private static function addSpecializedControl(
        WP_Customize_Manager $wpCustomize,
        string $settingId,
        array $field,
        array $controlArguments,
    ): bool {
        $controlClass = self::getSpecializedControlClass((string) ($field['type'] ?? ''));

        if ($controlClass === null || !class_exists($controlClass)) {
            return false;
        }

        $wpCustomize->add_control(new $controlClass($wpCustomize, $settingId, $controlArguments));

        return true;
    }

    /**
     * Get native specialized control class for a field type.
     *
     * @param string $fieldType Field type.
     *
     * @return string|null
     */
    private static function getSpecializedControlClass(string $fieldType): ?string
    {
        $controlClasses = [
            'code' => '\\WP_Customize_Code_Editor_Control',
            'color' => '\\WP_Customize_Color_Control',
            'image' => '\\WP_Customize_Image_Control',
            'upload' => '\\WP_Customize_Upload_Control',
        ];

        return $controlClasses[$fieldType] ?? null;
    }
}
