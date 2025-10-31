<?php

namespace Municipio\Helper;

class Sanitize
{
    public static function sanitizeATags(string $string)
    {
        return preg_replace('/<a[^>]*>(.*?)<\/a>/is', '$1', $string);
    }
}
