<?php

namespace Municipio\Helper;

/**
 * Class Data
 * @package Municipio\Helper
 */
class Data
{
    /**
     * Prepares structured data and applies Municipio/StructuredData filter.
     *
     * @param array|null $structuredData The structured data to be prepared.
     * @return string|null The prepared structured data as a JSON string, or null if the input is empty.
     */
    public static function normalizeStructuredData(?array $structuredData = []): ?string
    {
        $structuredData = apply_filters('Municipio/StructuredData', $structuredData);

        return json_encode($structuredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    /**
     * Alias for 'normalizeStructuredData' for legacy purposes.
     *
     * @param array $structuredData The structured data to be prepared.
     * @return array The prepared structured data.
     */
    public static function prepareStructuredData(array $structuredData = []): ?string
    {
        _doing_it_wrong(__METHOD__, 'The method ' . __METHOD__ . ' has been deprecated since version 3.61.8. Please use the normalizeStructuredData method instead.', '3.61.8');
        return self::normalizeStructuredData($structuredData);
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
