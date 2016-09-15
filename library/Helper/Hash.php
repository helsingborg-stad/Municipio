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
        $data = self::sanitizeData($data);
        return substr(base_convert(md5($data), 16, 32), 0, 12);
    }

    public static function sanitizeData($data)
    {
        if (!is_string($data)) {
            $data = serialize($data);
        }

        return $data;
    }
}
