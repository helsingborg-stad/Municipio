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

    /**
     * mkUniqueId
     * Creates a unique Id
     * @param int $length
     * @return false|string
     * @throws \Exception
     */
    public static function mkUniqueId($length = 8)
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($length / 2));
        } else if (function_exists("openssl_random_pseudo_bytes")) {

            $bytes = openssl_random_pseudo_bytes(ceil($length / 2), $isSourceStrong);
            if (false === $isSourceStrong || false === $bytes) {
                throw new \RuntimeException('IV generation failed');
            }

        } else {
            $bytes = mt_rand(0,99999999);
            throw new Exception("no cryptographically secure random id function available");
        }
        return substr(bin2hex($bytes), 0, $length);
    }
}
