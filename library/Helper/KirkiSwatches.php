<?php

namespace Municipio\Helper;

/**
 * Class KirkiSwatches
 */
class KirkiSwatches
{
    /**
     * Returns a color swatch array.
     *
     * @return array Colors
    */
    public static function getColors()
    {
        if (function_exists('get_theme_mod')) {
            return [
                get_theme_mod('color_palette_primary')['base']          ?? '#ae0b05',
                get_theme_mod('color_palette_primary')['dark']          ?? '#770000',
                get_theme_mod('color_palette_primary')['light']         ?? '#e84c31',
                get_theme_mod('color_palette_primary')['contrasting']   ?? '#ffffff',
                get_theme_mod('color_palette_secondary')['base']        ?? '#ec6701',
                get_theme_mod('color_palette_secondary')['dark']        ?? '#b23700',
                get_theme_mod('color_palette_secondary')['light']       ?? '#ff983e',
                get_theme_mod('color_palette_secondary')['contrasting'] ?? '#ffffff',
                get_theme_mod('color_background')['background']         ?? '#f5f5f5',
             ];
        }

        return [];
    }
}
