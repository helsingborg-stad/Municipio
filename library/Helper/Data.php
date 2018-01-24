<?php

namespace Municipio\Helper;

class Data
{
    public static function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public static function getRemoteJson($url)
    {
        $request = wp_remote_get($url);

        if (wp_remote_retrieve_response_code($request) == 200 && self::isJson(wp_remote_retrieve_body($request))) {
            return json_decode(wp_remote_retrieve_body($request));
        }

        return false;
    }
}
