<?php

namespace Municipio\Helper;

/**
 * Helper class for making RESTful requests.
 */
class RestRequestHelper
{
    /**
     * @param string $apiUrl
     * @return array|object|\WP_Error
     */
    public static function get(string $apiUrl)
    {
        try {
            $response = wp_remote_get($apiUrl);

            if (wp_remote_retrieve_response_code($response) !== 200) {
                throw new \Exception(wp_remote_retrieve_response_message($response));
            }

            $body = wp_remote_retrieve_body($response);
            $body = nl2br($body);
            $body = str_replace(array('\r\n', '\r', '\n'), array("<br>", "<br>", "<br>"), $body);

            $data = json_decode($body, false);
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage());
        }

        return $data;
    }

    /**
     * @param string $apiUrl
     * @return array|object|\WP_Error
     */
    public static function getHeaders(string $apiUrl)
    {
        try {
            $response        = wp_remote_get($apiUrl);
            $responseHeaders = wp_remote_retrieve_headers($response);

            if (!is_a($responseHeaders, 'WpOrg\Requests\Utility\CaseInsensitiveDictionary')) {
                throw new \Exception('No headers found');
            }

            $headers = $responseHeaders->getAll();
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage());
        }

        return $headers;
    }
}
