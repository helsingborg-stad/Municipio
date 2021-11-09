<?php

namespace Municipio\Helper;

/**
 * Class Color
 * @package Municipio\Customizer
 */
class Color
{
    /**
     * Get color palette from theme options
     *
     * @return array
     */
    public static function getPalette() {

        $colorTargetKeys = [
            'color_palette_primary',
            'color_palette_secondary'
        ];

        if(is_array($colorTargetKeys) && !empty($colorTargetKeys)) {
            
            $colors = []; $result = [];
            
            foreach ($colorTargetKeys as $key) {
                $colors[$key] = \Kirki::get_option($key);
            }

            //Flatten
            array_walk_recursive(
                $colors,
                function($v) use (&$result){ 
                    $result[] = $v; 
                }
            ); 

            return array_unique(array_merge($result, ["#ffffff", "#000000"])); 
        }

        return []; 
    }
}