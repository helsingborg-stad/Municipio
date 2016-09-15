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
        if (is_array($input) || is_object($input)) {
            return substr(base_convert(md5(serialize($input)), 16, 32), 0, 12);
        }

        return substr(base_convert(md5($input), 16, 32), 0, 12);
    }
}
