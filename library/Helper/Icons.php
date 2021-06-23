<?php

namespace Municipio\Helper;

class Icons
{
    /**
     * Get path to icons description file
     *
     * @return string
     */
    private static function getIconPath() : string {
        return MUNICIPIO_PATH . "assets/dist/data/ico.json"; 
    }

    /**
     * Read icons list
     *
     * @return array Array of icon strings
     */
    public function getIcons() {
        if(file_exists(self::getIconPath())) {

            if($contents = file_get_contents(self::getIconPath())) {
                $contents = json_decode($contents); 

                if(isset($contents->icons) && !empty($contents->icons)) {
                    return array_column(
                        (array) $contents->icons, 
                        'name'
                    ); 
                }

            }
        }

        return false; 
    }
}
