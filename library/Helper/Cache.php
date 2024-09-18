<?php

namespace Municipio\Helper;

class Cache
{
    /**
     * Fragment cache in memcached
     * @param  string $postId      The post id that you want to cache (or any other key that relates to specific data)
     * @param  string $module      Any input data altering output result as a concatinated string/array/object.
     * @param  string $ttl         The time that a cache should live (in seconds)
     * @return string              The request response
     */

    private $postId = null;
    private $ttl    = null;
    private $hash   = null;

    public static $keyGroup = 'mun-cache';

    public function __construct($postId, $module = '', $ttl = 3600 * 24)
    {

        // Set variables
        $this->postId = $postId;
        $this->ttl    = $ttl;

        // Create hash string
        $this->hash = $this->createShortHash($module);

        // Role based key
        if (is_user_logged_in() && isset(wp_get_current_user()->caps) && is_array(wp_get_current_user()->caps)) {
            $caps = wp_get_current_user()->caps;

            if (is_super_admin(get_current_user_id())) {
                $caps['superadmin'] = true;
            }

            $this->hash = $this->hash . "-auth-" . $this->createShortHash($caps, true);
        }
    }

    /**
     * Get keygroup
     * @param  integer $blogId Blog id (optional)
     * @return string          Key group
     */
    public static function getKeyGroup($blogId = null)
    {
        $keyGroup = self::$keyGroup;

        if (!function_exists('is_multisite') || !is_multisite()) {
            return $keyGroup;
        }

        if (is_null($blogId)) {
            $blogId = get_current_blog_id();
        }

        $keyGroup .= '-' . $blogId;
        return $keyGroup;
    }

    /**
     * Cleas the cache of a specific post id
     * @param  integer $postId Post id to clear
     * @return boolean
     */
    public static function clearCache($postId, $post)
    {
        if (wp_is_post_revision($postId) || get_post_status($postId) != 'publish') {
            return false;
        }

        wp_cache_delete($postId, self::getKeyGroup());
        wp_cache_delete($post->post_type, self::getKeyGroup());

        self::clearOembedCache($postId);

        // Empty post type for all sites in network (?)
        if (function_exists('is_multisite') && is_multisite() && apply_filters('Municipio\Cache\EmptyForAllBlogs', false, $post)) {
            $blogs = get_sites();

            foreach ($blogs as $blog) {
                wp_cache_delete($post->post_type, self::getKeyGroup($blog->blog_id));
            }
        }

        return true;
    }

    /**
     * Clears Oembed cache
     * @param  integer $postId Post id to clear
     */
    public static function clearOembedCache(int $postId)
    {
        global $wpdb;

        $metaKeyPattern = '_oembed_%';
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND post_id = %d",
                $metaKeyPattern,
                $postId
            )
        );
    }

    /**
     * Starts the "cache engine"
     * @return boolean Returns true if engine started or inactivated, returns false if previous cache is loaded
     */
    public function start()
    {
        if (!$this->isActive()) {
            return true;
        }

        if (!$this->hasCache()) {
            ob_start();
            return true;
        }

        $this->getCache(true);
        return false;
    }

    /**
     * Stops the cache engine and saves the output buffer to the cache
     * @return boolean
     */
    public function stop()
    {
        if (!$this->isActive() || $this->hasCache()) {
            return false;
        }

        // Get output buffer and save to cache
        $return_data = ob_get_clean();

        if (!empty($return_data)) {
            $cacheArray = (array) wp_cache_get($this->postId, self::getKeyGroup());

            $cacheArray[$this->hash] = $return_data . $this->fragmentTag();

            wp_cache_delete($this->postId, self::getKeyGroup());

            wp_cache_add($this->postId, array_filter($cacheArray), self::getKeyGroup(), $this->ttl);
        }

        echo $return_data;
        return true;
    }

    /**
     * Check if has cache
     * @return boolean
     */
    private function hasCache()
    {
        if (!$this->isActive()) {
            return false;
        }

        return !empty($this->getCache(false));
    }

    /**
     * Get cache
     * @param  boolean $print Set to true to print instead of return
     * @return mixed
     */
    private function getCache($print = true)
    {
        $cacheArray = wp_cache_get($this->postId, self::getKeyGroup());

        if (!is_array($cacheArray) || !array_key_exists($this->hash, $cacheArray)) {
            return false;
        }

        if ($print === true) {
            echo $cacheArray[$this->hash];
        }

        return $cacheArray[$this->hash];
    }

    /**
     * Output fragment cache fingerprint in source code
     * @return void
     */
    private function fragmentTag()
    {
        return '<!-- FGC: [' . current_time("Y-m-d H:i:s", 1) . '| ' . $this->hash . ']-->';
    }

    /**
     * Check if cache engine shoud be used
     * @return boolean
     */
    private function isActive()
    {
        if (defined('MUNICIPIO_FRAGMENT_CACHE') && !MUNICIPIO_FRAGMENT_CACHE) {
            return false;
        }

        return true;
    }

    /**
     * Create a short hash from a value
     * @param  string  $input    Key
     * @param  boolean $keysOnly Set to true for keys only
     * @return string            Hash
     */
    private function createShortHash($input, $keysOnly = false)
    {
        if ($keysOnly === true && (is_array($input) || is_object($input))) {
            $input = array_keys($input);
        }

        if (is_array($input) || is_object($input)) {
            $input = substr(base_convert(md5(serialize($input)), 16, 32), 0, 12);
            return $input;
        }

        $input = substr(base_convert(md5($input), 16, 32), 0, 12);
        return $input;
    }
}

/*
Usage example:
$cache = new Municipio\Helper\Cache($post->Id);
if ($cache->start()) {
    // Your cacheable content here
    $cache->stop();
}
*/
