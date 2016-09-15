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
        if (is_array($data) || is_object($data)) {
            return substr(base_convert(md5(serialize($data)), 16, 32), 0, 12);
        }

        return substr(base_convert(md5($data), 16, 32), 0, 12);
    }
}
