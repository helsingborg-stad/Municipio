<?php

namespace Municipio\Customizer;

/**
 * Class Colors
 * @package Municipio\Customizer
 */
class Width
{
    /**
     * Prepare the color and alpha value
     * @return string
     */
    public function getWidth() {

        if(is_front_page()) {
            //get_field('', ); 
        }
    }

    /**
     * Convert the hexadecimal value to rgba
     * @return string
     */
    private function convertHexToRgb($value, $alpha, $default) {  
        $value = !empty($value) ? $value : $default;  
        $value = sscanf($value, "#%02x%02x%02x");
        return "rgba({$value[0]},{$value[1]},{$value[2]}, $alpha)";
    }
}