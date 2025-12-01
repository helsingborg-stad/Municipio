<?php

namespace Municipio\Helper;

/**
 * Class KirkiSwatches
 */
class KirkiSwatches
{
    /**
     * Cached color swatches.
     *
     * @var array|null
     */
    public static $cachedColors = null;

    /**
     * Returns a color swatch array.
     *
     * @return array Colors
     */
    public static function getColors()
    {
        // Check if colors are already cached
        if (self::$cachedColors !== null) {
            return self::$cachedColors;
        }

        // Compute colors if not cached
        if (function_exists('get_theme_mod')) {
            $colorPalettePrimary    = get_theme_mod('color_palette_primary');
            $colorPaletterSecondary = get_theme_mod('color_palette_secondary');
            $colorBackground        = get_theme_mod('color_background')['background'] ?? null;

            self::$cachedColors = [
                $colorPalettePrimary['base']           ?? '#ae0b05',
                $colorPalettePrimary['dark']           ?? '#770000',
                $colorPalettePrimary['light']          ?? '#e84c31',
                $colorPalettePrimary['contrasting']    ?? '#ffffff',
                $colorPaletterSecondary['base']        ?? '#ec6701',
                $colorPaletterSecondary['dark']        ?? '#b23700',
                $colorPaletterSecondary['light']       ?? '#ff983e',
                $colorPaletterSecondary['contrasting'] ?? '#ffffff',
                $colorBackground                       ?? '#f5f5f5',
            ];
        } else {
            self::$cachedColors = [];
        }

        return self::$cachedColors;
    }
}
