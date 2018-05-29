<?php

namespace Municipio\Helper;

class Hash
{
    /**
     * Create a shorthand hash of data input
     * @param  mixed $data  Data to hash
     * @return string       Hash
     */
    public static function short($data)
    {
        return substr(base_convert(md5(self::sanitizeData($data)), 16, 32), 0, 12);
    }

    /**
     * Sanitizes (serializes) the hash input data if needed
     * @param  mixed   $data   Input data
     * @return string          Sanitized data
     */
    public static function sanitizeData($data)
    {
        if (!is_string($data)) {
            $data = serialize($data);
        }
        return $data;
    }
}
