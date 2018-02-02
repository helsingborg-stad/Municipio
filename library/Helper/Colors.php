<?php

namespace Municipio\Helper;

class Colors
{
    public static function themeColors()
    {   $colors = get_option('color_scheme_palette');

        return $colors;
    }

    public static function neturalColors()
    {
        $colors = array('#000000', '#222222', '#444444', '#666666', '#888888', '#aaaaaa', '#cccccc', '#eeeeee', '#ffffff');

        return $colors;
    }
}
