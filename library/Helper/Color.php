<?php

namespace Municipio\Helper;

/**
 * Class Color
 * @package Municipio\Customizer
 */
class Color
{
    /**
     * Prepare the color and alpha value
     *
     * @param   array   $colorItem      [
     *                                      ['value' => ['color', 'alpha'],
     *                                      ['default' => ['color' => '', 'alpha' => '']]
     *                                  ]
     *
     * @return  string                  Rgba css color value.
     */
    public static function prepareColor($colorItem)
    {

        $colorItem['alpha'] = "1"; //Set default alpha value

        if (is_array($colorItem['value']) || (empty($colorItem['value']) && is_array($colorItem['default']))) {
            //Extra default values for group
            $defaultColor = $colorItem['default']['color'] ?? "";
            $defaultAlpha = $colorItem['default']['alpha'] . "%" ?? "1";

            //Collect set values for group
            if (is_array($colorItem['value'])) {
                $setColor = array_values($colorItem['value'])[0];
                $setAlpha = array_values($colorItem['value'])[1];
            } else {
                $setColor = $colorItem['value'];
                $setAlpha = $colorItem['alpha'];
            }

            //Define set value else default values
            $colorItem['value'] = !empty($setColor) ? $setColor : $defaultColor;
            $colorItem['alpha'] = !empty($setAlpha) || $setAlpha == "0" ? $setAlpha . '%' : $defaultAlpha; //empty() returns true on "0"
        }

        return self::convertHexToRgba($colorItem['value'], $colorItem['alpha'], $colorItem['default']);
    }

    /**
     * Convert the hexadecimal value to rgba
     * @return string
     */
    private static function convertHexToRgba($value, $alpha, $default)
    {
        $value = !empty($value) ? $value : $default;
        $value = sscanf($value, "#%02x%02x%02x");
        return "rgba({$value[0]}, {$value[1]}, {$value[2]}, $alpha)";
    }

    public static function getPalettes(array $options = [
        'color_palette_primary',
        'color_palette_secondary',
        'color_palette_complement',
        'color_background',
        'color_palette_monotone'
    ])
    {
        $colorPalettes = apply_filters('Municipio/Helper/Color/options', $options);

        if (!class_exists('Kirki') || empty($colorPalettes)) {
            return $colorPalettes;
        }

        foreach ($options as $option) {
            $value = \Kirki::get_option($option);
            if (is_array($value) && !empty($value)) {
                $colorPalettes[$option] = $value;
            }
        }

        return apply_filters('Municipio/Helper/Color/colorPalettes', $colorPalettes);
    }
}
