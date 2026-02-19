<?php

namespace Municipio\Upgrade\Version\Helper;

use WP_CLI;

class SetAssociativeThemeMod
{
    
    /**
     * A simple wrapper around set_theme_mod() in order to set a single property value of an associative array setting.
     * Key should include a dot in order to target a property.
     * eg. color_palette.primary will target array('primary' => VALUE).
     *
     * Does not support nested values eg settings.property.nested_value_1.nested_value_2 etc
     *
     * @param string $key
     * @param string $value
     * @param bool $castToArray this will transform existing values which are not arrays to empty arrays when true
     * @return bool True if the value was updated, false otherwise.
     */
    public static function set($key, $value, $castToArray = false)
    {
        $parsedString = explode('.', $key);
        $key          = $parsedString[0] ?? '';
        $property     = $parsedString[1] ?? '';

        if (empty($parsedString) || empty($key)) {
            return;
        }

        if (!empty($property)) {
            $associativeArr = get_theme_mod($key, []);
            $associativeArr = is_array($associativeArr) || $castToArray !== true ? $associativeArr : [];

            if (!is_array($associativeArr)) {
                $errorMessage = "Failed to migrate setting (" . $key . "." . $property . ").
                The specified setting already exists and is not an associative array.";
                WP_CLI::line($errorMessage);
                return;
            }

            $associativeArr[$property] = $value;
            $value                     = $associativeArr;
        }

        return set_theme_mod($key, $value);
    }
}