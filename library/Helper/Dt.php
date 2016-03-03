<?php

namespace Municipio\Helper;

class Dt
{
    public static function toStringFormat($time)
    {
        if (function_exists('mysql2date')) {
            return mysql2date('j F Y \k\l\. H:i', date('Y-m-d H:i:s', $time));
        }

        return date('j F Y \k\l\. H:i', $time);
    }

    public static function dateWithTime($time)
    {
        if (function_exists('mysql2date')) {
            return mysql2date('Y-m-d \k\l\. H:i', date('Y-m-d H:i:s', $time));
        }

        return date('Y-m-d \k\l\. H:i', $time);
    }
}
