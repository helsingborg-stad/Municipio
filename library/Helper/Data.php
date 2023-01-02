<?php

namespace Municipio\Helper;

class Data
{

    public static function getStructuredData($postType, $postId)
    {
        $schema = apply_filters('Municipio/StructuredData', [], $postType, $postId);
     
        if (is_null($schema)) {
            return false;
        }

        if (empty($schema)) {
            return false;
        }

        //Default common schema
        $schema = array_merge(
            ["@context" => "https://schema.org/"],
            $schema
        );

        return json_encode($schema);
    }
    public static function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    public static function getRemoteJson($url)
    {
        $args = array();

        if (defined('DEV_MODE') && DEV_MODE) {
            $args['sslverify'] = false;
        }

        $request = wp_remote_get($url, $args);

        if (wp_remote_retrieve_response_code($request) == 200 && self::isJson(wp_remote_retrieve_body($request))) {
            return json_decode(wp_remote_retrieve_body($request));
        }

        return false;
    }
}
