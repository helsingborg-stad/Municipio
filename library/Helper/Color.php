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

    public static function getBestContrastColor(string $color, bool $returnAnyColor = false): string
    {
        [$r, $g, $b] = self::hexToRgb($color);
        $bgLuminance = self::getRelativeLuminance($r, $g, $b);

        $whiteContrast = self::getContrastRatio($bgLuminance, self::getRelativeLuminance(255, 255, 255));
        $blackContrast = self::getContrastRatio($bgLuminance, self::getRelativeLuminance(0, 0, 0));

        if (!$returnAnyColor) {
            return $whiteContrast >= $blackContrast ? 'white' : 'black';
        }

        [$h, $s, $l] = self::rgbToHsl($r, $g, $b);
        $l = $l < 0.5 ? 0.95 : 0.05;
        [$rNew, $gNew, $bNew] = self::hslToRgb($h, $s, $l);

        return sprintf("#%02x%02x%02x", $rNew, $gNew, $bNew);
    }

    private static function getContrastRatio(float $lum1, float $lum2): float
    {
        return ($lum1 > $lum2)
            ? ($lum1 + 0.05) / ($lum2 + 0.05)
            : ($lum2 + 0.05) / ($lum1 + 0.05);
    }

    private static function getRelativeLuminance(int $r, int $g, int $b): float
    {
        [$r, $g, $b] = array_map(function ($c) {
            $c = $c / 255;
            return $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
        }, [$r, $g, $b]);

        return 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    }

    private static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0]
                 . $hex[1] . $hex[1]
                 . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    private static function rgbToHsl(int $r, int $g, int $b): array
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $h = $s = $l = ($max + $min) / 2;

        if ($max !== $min) {
            $d = $max - $min;
            $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

            switch ($max) {
                case $r:
                    $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $h = ($b - $r) / $d + 2;
                    break;
                case $b:
                    $h = ($r - $g) / $d + 4;
                    break;
            }

            $h /= 6;
        } else {
            $h = $s = 0;
        }

        return [$h, $s, $l];
    }

    private static function hslToRgb(float $h, float $s, float $l): array
    {
        if ($s == 0) {
            $val = round($l * 255);
            return [$val, $val, $val];
        }

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        return [
            round(self::hue2rgb($p, $q, $h + 1/3) * 255),
            round(self::hue2rgb($p, $q, $h) * 255),
            round(self::hue2rgb($p, $q, $h - 1/3) * 255)
        ];
    }

    private static function hue2rgb(float $p, float $q, float $t): float
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1/2) return $q;
        if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
        return $p;
    }
}
