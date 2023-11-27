<?php

namespace Municipio\Helper;

class RestRequestHelper
{
    private const CACHE_GROUP = 'municipioRestRequest';
    private const CACHE_KEY_PREFIX = 'request_';
    private const CACHE_TTL        = HOUR_IN_SECONDS;

    /**
     * @param string $apiUrl
     * @return array|object|\WP_Error
     */
    public static function getFromApi(string $apiUrl)
    {
        $cacheKey   = self::getCacheKey($apiUrl);
        $cacheValue = self::getFromCache($cacheKey);

        if ($cacheValue) {
            return $cacheValue;
        }

        try {
            $response = wp_remote_get($apiUrl);

            if( wp_remote_retrieve_response_code($response) !== 200 ) {
                throw new \Exception(wp_remote_retrieve_response_message($response));
            }

            $body     = wp_remote_retrieve_body($response);
            $data     = json_decode($body, false);
            self::setCache($cacheKey, $data);
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
        $cacheKey   = self::getCacheKey($apiUrl) . '-headers';
        $cacheValue = self::getFromCache($cacheKey);

        if ($cacheValue) {
            return $cacheValue;
        }

        try {
            $response = wp_remote_get($apiUrl);
            $responseHeaders  = wp_remote_retrieve_headers($response);

            if (!is_a($responseHeaders, 'WpOrg\Requests\Utility\CaseInsensitiveDictionary')) {
                throw new \Exception('No headers found');
            }

            $headers = $responseHeaders->getAll();
            self::setCache($cacheKey, $headers);

        } catch (\Exception $e) {
            return new \WP_Error('rest_error', $e->getMessage());
        }

        return $headers;
    }

    private static function getCacheKey($apiUrl): string
    {
        return self::CACHE_KEY_PREFIX . md5($apiUrl);
    }

    private static function setCache(string $cacheKey, $data): void
    {
        if (!empty($cacheKey)) {
            wp_cache_set($cacheKey, $data, self::CACHE_GROUP, self::CACHE_TTL);
        }
    }

    private static function getFromCache(string $cacheKey)
    {
        return wp_cache_get($cacheKey, self::CACHE_GROUP);
    }
}
