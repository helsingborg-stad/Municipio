<?php

namespace Municipio\Helper;

/**
 * Class Icons
 */
class Icons
{
    /**
     * Get path to icons description file
     *
     * @return string
     */
    private static function getIconPath(): string
    {
        return MUNICIPIO_PATH . "assets/generated/icon.json";
    }

    /**
     * Read icons list
     *
     * @return array Array of icon strings
     */
    public static function getIcons()
    {
        if (file_exists(self::getIconPath())) {
            if ($contents = file_get_contents(self::getIconPath())) {
                $icons = json_decode($contents);

                return $icons;
            }
        }

        return false;
    }
}
