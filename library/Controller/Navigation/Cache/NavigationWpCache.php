<?php

namespace Municipio\Controller\Navigation\Cache;

class NavigationWpCache
{
    private static $cacheGroup  = 'municipioNavMenu';
    private static $cacheExpire = 60 * 15; // 15 minutes
    private static $cache       = [];

    /**
     * Store in cache
     *
     * @param string $key   The key to store in
     * @param mixed $value  The value to store
     * @return mixed
     */
    public static function setCache(string $key, mixed $data, bool $persistent = true): bool
    {
        //Runtime
        self::$cache[$key] = $data;

        //Persistent
        if ($persistent) {
            //Add to cache group (enables purging/banning)
            if (self::setcacheGroup($key)) {
                //Store cache
                return wp_cache_set($key, $data, '', self::$cacheExpire);
            }

            return false;
        }

        return true;
    }

    /**
     * Keep track of what's has been cached
     *
     * @param string $newCacheKey
     * @return boolean
     */
    public static function setCacheGroup(string $newCacheKey): bool
    {
        //Create new addition
        $cacheObject = [$newCacheKey];

        //Get old cache
        $previousCachedObject = wp_cache_get(self::$cacheGroup);
        if (is_array($previousCachedObject) && !empty($previousCachedObject)) {
            $cacheObject = array_merge($cacheObject, $previousCachedObject);
        }

        return wp_cache_set(self::$cacheGroup, array_unique($cacheObject));
    }

    /**
     * Get from cache
     *
     * @param The cache key $key
     * @return mixed
     */
    public static function getCache($key, $persistent = true): mixed
    {
        //Get runtime cache
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        //Get persistent cache, store runtime
        if ($persistent) {
            return self::$cache[$key] = wp_cache_get($key);
        }

        return null;
    }
}