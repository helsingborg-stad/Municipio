<?php

namespace Municipio\Helper;

class Input
{
    /**
     * Checks if a variable isset and is not empty
     * @param  mixed  $key Variable
     * @return boolean
     */
    public static function hasValue($key)
    {
        return isset($key) && !empty($key);
    }
}
