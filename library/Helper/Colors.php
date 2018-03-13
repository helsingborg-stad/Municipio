<?php

namespace Municipio\Helper;

class Colors
{
    public static function themeColors()
    {
        if (!function_exists('get_option')) {
            return self::neturalColors();
        }
        return (array) get_option('color_scheme_palette');
    }

    public static function neturalColors()
    {
        $colors = array('#000000', '#222222', '#444444', '#666666', '#888888', '#aaaaaa', '#cccccc', '#eeeeee', '#ffffff');

        return $colors;
    }
}
