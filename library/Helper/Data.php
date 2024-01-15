<?php

namespace Municipio\Helper;

/**
 * Class Data
 * @package Municipio\Helper
 */
class Data
{
    /**
     * Prepares structured data for encoding as JSON.
     *
     * @param array $structuredData The structured data to be prepared.
     * @return false|string The encoded JSON string of the prepared
     * structured data, or false if the structured data is empty.
     */
    public static function prepareStructuredData(array $structuredData = [])
    {
        if (empty($structuredData)) {
            return false;
        }

        $structuredData = apply_filters('Municipio/StructuredData', $structuredData);

        foreach ($structuredData as $key => $value) {
            if (empty($value)) {
                unset($structuredData[$key]);
            }
        }

        if (empty($structuredData)) {
            return false;
        }

        $schema             = [];
        $schema["@context"] = "http://schema.org";

        if (count($structuredData) > 1) {
            $schema["@graph"] = [];
            foreach ($structuredData as $key => $properties) {
                if (empty($properties)) {
                    continue;
                }
                array_push($schema["@graph"], $properties);
            }
        } else {
            $schema = $structuredData;
        }

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    /**
     * Checks if a string is a valid JSON.
     *
     * @param string $string The string to be checked.
     * @return bool Returns true if the string is a valid JSON, false otherwise.
     */
    public static function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    /**
     * Retrieves a JSON response from a remote URL.
     *
     * @param string $url The URL to retrieve the JSON from.
     * @return mixed The decoded JSON response, or false if the response is not valid JSON.
     */
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
