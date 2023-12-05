<?php

namespace Municipio\Helper;

class RestRequestHelper
{
    /**
     * @param string $apiUrl
     * @return array|object|\WP_Error
     */
    public static function getFromApi(string $apiUrl)
    {
        try {

            $response = wp_remote_get($apiUrl);

            if( wp_remote_retrieve_response_code($response) !== 200 ) {
                throw new \Exception(wp_remote_retrieve_response_message($response));
            }

            $body     = wp_remote_retrieve_body($response);
            $data     = json_decode($body, false);
        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage());
        }

        return $data;
    }
    
    /**
     * @param string $apiUrl
     * @return array|object|\WP_Error
     */
    public static function getHeadersFromApi(string $apiUrl)
    {
        try {
            $response = wp_remote_get($apiUrl);
            $responseHeaders  = wp_remote_retrieve_headers($response);

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
