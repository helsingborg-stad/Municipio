<?php

namespace Municipio\Helper;

/**
 * File helper class to handle file operations such as checking existence,
 * getting URLs, and retrieving image dimensions.
 *
 * @package Municipio\Helper
 */
class File
{
    /**
     * Runtime cache for file existence checks to avoid repeated cache lookups within same request
     */
    private static array $runtimeFileExistsCache = [];

    /**
     * It takes a filename and returns the URL of the file if it exists in the media library
     *
     * @param string filename The name of the file you want to get the URL for.
     *
     * @return ?string The URL of the file.
     */
    public static function getFileUrl(string $filename): ?string
    {
        global $wpdb;
        $meta = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT post_id FROM `$wpdb->postmeta`
                WHERE `meta_key` LIKE '_wp_attached_file'
                AND `meta_value` LIKE '%$filename%'
                LIMIT 1"
            )
        );
        if (!empty($meta)) {
            return \wp_get_attachment_url($meta[0]);
        }
        return null;
    }

    /**
     * Check if a file exists, cache in Redis (or other wp_cache backend).
     *
     * @param string  $filePath       The file path
     * @param integer $expireFound    TTL for positive result
     * @param integer $expireNotFound TTL for negative result
     *
     * @return bool If the file exists or not
     */
    public static function fileExists($filePath, $expireFound = 86400, $expireNotFound = 86400)
    {
        // Runtime cache (per-request)
        if (isset(self::$runtimeFileExistsCache[$filePath])) {
            return self::$runtimeFileExistsCache[$filePath];
        }

        // Build cache key + group
        $key   = md5($filePath);
        $group = 'municipio_file_exists_cache';

        $wpService = WpService::get();

        // Check persistent cache
        $cachedValue = $wpService->wpCacheGet($key, $group);

        if ($cachedValue !== false) {
            if ($cachedValue === 'found') {
                self::$runtimeFileExistsCache[$filePath] = true;
                return true;
            }
            if ($cachedValue === 'not_found') {
                self::$runtimeFileExistsCache[$filePath] = false;
                return false;
            }
            // If unexpected value, fall through and re-check filesystem
        }

        // Not in cache → check filesystem
        if (file_exists($filePath)) {
            $wpService->wpCacheSet($key, 'found', $group, $expireFound);
            self::$runtimeFileExistsCache[$filePath] = true;
            return true;
        }

        // File not found
        $wpService->wpCacheSet($key, 'not_found', $group, $expireNotFound);
        self::$runtimeFileExistsCache[$filePath] = false;
        return false;
    }

    /**
     * Get the dimensions of an image file and cache the result for future use.
     *
     * This function retrieves the dimensions (width and height) of the specified
     * image file located at the given file path. It first checks if the result is
     * already cached to improve performance. If the dimensions are not cached,
     * it attempts to retrieve them using the PHP built-in function `getimagesize`,
     * and then caches the result for future use.
     *
     * @param string $filePath The file path of the image for which to retrieve dimensions.
     * @param   integer Time to store positive result
     * @param   integer Time to store negative result
     *
     * @return array|false Returns an array containing the image dimensions (width and height)
     *                    if the image file is found and dimensions are successfully
     *                    retrieved. Returns false if the file is not found or if
     *                    dimensions cannot be determined.
     */
    public static function getImageSize($filePath, $expireFound = 0, $expireNotFound = 86400)
    {
        //Unique cache value
        $uid = "municipio_get_image_size_cache_" . md5($filePath);

        //If in cahce, found
        if ($cachedValue = wp_cache_get($uid)) {
            return $cachedValue;
        }

        //If not in cache, look for it, if found cache.
        if ($imageSize = getimagesize($filePath)) {
            wp_cache_set($uid, $imageSize, '', $expireFound);
            return $imageSize;
        }

        //Opsie, file not found
        wp_cache_set($uid, false, '', $expireNotFound);
        return false;
    }

    /**
     * Get the MIME type of a file and cache the result for future use.
     *
     * This function retrieves the MIME type of the specified file located at the given
     * file path. It first checks if the result is already cached to improve performance.
     * If the MIME type is not cached, it attempts to retrieve it using the `mime_content_type`
     * function and then caches the result for future use.
     *
     * @param string $filePath The file path for which to retrieve the MIME type.
     *
     * @return string|false Returns the MIME type of the file if it is found and the MIME
     *                     type is successfully determined. Returns false if the file is
     *                     not found or if the MIME type cannot be determined.
     */
    public static function getMimeType($filePath)
    {
        //Unique cache value
        $uid = "municipio_get_mime_cache_" . md5($filePath);

        //If in cahce, found
        if ($cachedValue = wp_cache_get($uid)) {
            return $cachedValue;
        }

        //If not in cache, look for it, if found cache.
        if ($mime = mime_content_type($filePath)) {
            wp_cache_set($uid, $mime);
            return $mime;
        }

        //Opsie, file not found
        return false;
    }

    /**
     * Clear runtime cache for file existence checks (useful for testing)
     */
    public static function clearRuntimeCache(): void
    {
        self::$runtimeFileExistsCache = [];
    }
}
