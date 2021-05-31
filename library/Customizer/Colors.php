<?php

namespace Municipio\Customizer;

/**
 * Class Colors
 * @package Municipio\Customizer
 */
class Colors
{
    /**
     * Prepare the color and alpha value
     * @return string
     */
    public function prepareColor ($colorItem) {
    
        $colorItem['alpha'] = "1"; //Set default alpha value

        if(is_array($colorItem['value'])||(empty($colorItem['value']) && is_array($colorItem['default']))) {

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
            $colorItem['alpha'] = !empty($setAlpha) || $setAlpha == "0" ? $setAlpha .'%' : $defaultAlpha; //empty() returns true on "0"                        

        } 

        return $this->convertHexToRgb($colorItem['value'], $colorItem['alpha'], $colorItem['default']);
    }

    /**
     * Convert the hexadecimal value to rgba
     * @return string
     */
    private function convertHexToRgb($value, $alpha, $default) {  
        $value = !empty($value) ? $value : $default;  
        $value = sscanf($value, "#%02x%02x%02x");
        return "rgba({$value[0]}, {$value[1]}, {$value[2]}, $alpha)";
    }
}