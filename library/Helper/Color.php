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
     *                                      'value' => ['color', 'alpha'],
     *                                      'default' => ['color' => '', 'alpha' => '']
     *                                  ]
     *
     * @return  string                  Rgba css color value.
     */
    public static function prepareColor(array $colorItem)
    {
        $colorItem['alpha'] = "1";

        if (
            isset($colorItem['value']) && is_array($colorItem['value']) ||
            isset($colorItem['default']) && is_array($colorItem['default'])
        ) {
            $defaultColor = !empty($colorItem['default']['color']) ? $colorItem['default']['color'] : "";
            $defaultAlpha = !empty($colorItem['default']['alpha']) ? $colorItem['default']['alpha'] :  "1";

            if (!empty($colorItem['value'])) {
                $setColor = array_values($colorItem['value'])[0] ?? '';
                $setAlpha = array_values($colorItem['value'])[1] ?? "1";
            }

            $colorItem['value'] = !empty($setColor) ? $setColor : $defaultColor;
            $colorItem['alpha'] = isset($setAlpha) && $setAlpha == "0" || !empty($setAlpha) ? $setAlpha : $defaultAlpha;
        } else {
            return false;
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

    /**
     * Get color palettes based on specified options.
     *
     * @param array $options An array of option names to retrieve color palettes.
     * Defaults to common color palette options.
     *
     * @return array An associative array of color palettes,
     * where keys are option names and values are corresponding color palettes.
     *
     */
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

    /**
     * Check if an RGBA color is visible.
     *
     * @param string $rgba The rgba color value (e.g., 'rgba(255, 255, 255, 0.5)').
     * @return bool False if the color is not visible, cannot be parsed, or is uncertain; true if visible.
     */
    public static function isRgbaVisible(?string $rgba): bool{

        if($rgba === null){
            return false;
        }

        if($rgba === 'transparent'){
            return false;
        }

        if (empty($rgba)) {
            return false;
        }

        // Match rgba values using a regex
        if (preg_match('/rgba\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(0|0?\.\d+|1(\.0)?)\s*\)/', $rgba, $matches)) {
            $alpha = (float) $matches[4];
            // Check if alpha is greater than 0
            return $alpha > 0;
        }

        // Return false if the value cannot be parsed or is invalid
        return false;
    }


}
