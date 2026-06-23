<?php

namespace Municipio\Customizer;

use Municipio\Customizer\Controls\AlphaColor\AlphaColorControl;
use Municipio\Customizer\Controls\Background\BackgroundControl;
use Municipio\Customizer\Controls\CustomContent\CustomContentControl;
use Municipio\Customizer\Controls\Divider\DividerControl;
use Municipio\Customizer\Controls\Headline\HeadlineControl;
use Municipio\Customizer\Controls\MultiCheck\MultiCheckControl;
use Municipio\Customizer\Controls\MultiColor\MultiColorControl;
use Municipio\Customizer\Controls\MultiSelect\MultiSelectControl;
use Municipio\Customizer\Controls\Repeater\RepeaterControl;
use WP_Customize_Manager;

class CustomFieldControlFactory
{
    private const CONTROL_CLASSES = [
        'alpha_color' => AlphaColorControl::class,
        'background' => BackgroundControl::class,
        'custom' => CustomContentControl::class,
        'divider' => DividerControl::class,
        'headline' => HeadlineControl::class,
        'multicheck' => MultiCheckControl::class,
        'multicolor' => MultiColorControl::class,
        'multiselect' => MultiSelectControl::class,
        'repeater' => RepeaterControl::class,
        'sortable' => '\\Municipio\\Customizer\\Controls\\Sortable\\SortableControl',
    ];

    /**
     * Add a Municipio custom control for a field.
     *
     * @param WP_Customize_Manager $wpCustomize WordPress Customizer manager.
     * @param string               $settingId   Setting identifier.
     * @param array                $field       Field configuration.
     *
     * @return void
     */
    public static function addControl(WP_Customize_Manager $wpCustomize, string $settingId, array $field): void
    {
        $controlClass = self::getControlClass($field);

        if ($controlClass === null) {
            return;
        }

        $wpCustomize->add_control(new $controlClass($wpCustomize, $settingId, CustomFieldControlArguments::fromField($field)));
    }

    /**
     * Resolve the control class for a field.
     *
     * @param array $field Field configuration.
     *
     * @return class-string|null
     */
    private static function getControlClass(array $field): ?string
    {
        return self::CONTROL_CLASSES[self::getControlType($field)] ?? null;
    }

    /**
     * Resolve the normalized control type for a field.
     *
     * @param array $field Field configuration.
     *
     * @return string
     */
    private static function getControlType(array $field): string
    {
        if (($field['type'] ?? '') === 'select' && self::hasMultipleValues($field)) {
            return 'multiselect';
        }

        if (($field['type'] ?? '') === 'color') {
            return 'alpha_color';
        }

        return (string) ($field['type'] ?? '');
    }

    /**
     * Determine if a field stores multiple selected values.
     *
     * @param array $field Field configuration.
     *
     * @return bool
     */
    private static function hasMultipleValues(array $field): bool
    {
        return isset($field['multiple']) && $field['multiple'] !== false && $field['multiple'] !== 0 && $field['multiple'] !== '0';
    }
}
