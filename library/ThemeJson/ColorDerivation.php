<?php

namespace Municipio\ThemeJson;

use Municipio\Helper\Color;

class ColorDerivation
{
    /**
     * Derive dark, light, and contrasting color variants from a base color.
     *
     * @param string $baseColor Hex color value (e.g., '#ae0b05')
     * @return array{dark: string, light: string, contrasting: string}
     */
    public static function deriveVariants(string $baseColor): array
    {
        return [
            'dark'        => self::darken($baseColor, 15),
            'light'       => self::lighten($baseColor, 15),
            'contrasting' => Color::getBestContrastColor($baseColor, true),
        ];
    }

    /**
     * Darken a hex color by a percentage.
     *
     * @param string $hex Hex color value
     * @param int $percent Percentage to darken (0-100)
     * @return string Darkened hex color
     */
    public static function darken(string $hex, int $percent): string
    {
        return self::adjustBrightness($hex, -$percent);
    }

    /**
     * Lighten a hex color by a percentage.
     *
     * @param string $hex Hex color value
     * @param int $percent Percentage to lighten (0-100)
     * @return string Lightened hex color
     */
    public static function lighten(string $hex, int $percent): string
    {
        return self::adjustBrightness($hex, $percent);
    }

    /**
     * Adjust brightness of a hex color.
     *
     * @param string $hex Hex color value
     * @param int $percent Positive to lighten, negative to darken
     * @return string Adjusted hex color
     */
    private static function adjustBrightness(string $hex, int $percent): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $factor = $percent / 100;

        if ($factor > 0) {
            // Lighten: move toward 255
            $r = $r + ((255 - $r) * $factor);
            $g = $g + ((255 - $g) * $factor);
            $b = $b + ((255 - $b) * $factor);
        } else {
            // Darken: move toward 0
            $factor = abs($factor);
            $r = $r * (1 - $factor);
            $g = $g * (1 - $factor);
            $b = $b * (1 - $factor);
        }

        $r = max(0, min(255, round($r)));
        $g = max(0, min(255, round($g)));
        $b = max(0, min(255, round($b)));

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /**
     * Check if a string is a valid hex color.
     *
     * @param string $color Color string to validate
     * @return bool True if valid hex color
     */
    public static function isValidHexColor(string $color): bool
    {
        return (bool) preg_match('/^#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $color);
    }

    /**
     * Normalize a color value (handle rgba, rgb, or hex).
     * Returns hex if possible, original value otherwise.
     *
     * @param string $color Color value to normalize
     * @return string|null Hex color or null if not convertible
     */
    public static function normalizeToHex(string $color): ?string
    {
        $color = trim($color);

        // Already hex
        if (self::isValidHexColor($color)) {
            return strpos($color, '#') === 0 ? $color : '#' . $color;
        }

        // Handle rgb/rgba
        if (preg_match('/^rgba?\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/', $color, $matches)) {
            return sprintf('#%02x%02x%02x', (int) $matches[1], (int) $matches[2], (int) $matches[3]);
        }

        return null;
    }
}
