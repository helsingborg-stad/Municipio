<?php

namespace Municipio\Helper;

/*
 * Sanitize helper class
 */
class Sanitize
{
    /**
     * Sanitize a string by removing all <a> tags but keeping the inner text
     *
     * @param string $string
     * @return string
     */
    public static function sanitizeATags(string $string)
    {
        return preg_replace('/<a[^>]*>(.*?)<\/a>/is', '$1', $string);
    }
}
