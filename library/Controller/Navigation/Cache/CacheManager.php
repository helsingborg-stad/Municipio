<?php

namespace Municipio\Controller\Navigation\Cache;

use Municipio\Controller\Navigation\Cache\CacheManagerInterface;

class CacheManager implements CacheManagerInterface
{
    private $cacheGroup  = 'municipioNavMenu';
    private $cacheExpire = 60 * 15; // 15 minutes
    private $cache       = [];

    /**
     * Store in cache
     *
     * @param string $key   The key to store in
     * @param mixed $value  The value to store
     * @return mixed
     */
    public function setCache(string $key, mixed $data, bool $persistent = true): bool
    {
        //Runtime
        $this->cache[$key] = $data;

        //Persistent
        if ($persistent) {
            //Add to cache group (enables purging/banning)
            if ($this->setcacheGroup($key)) {
                //Store cache
                return wp_cache_set($key, $data, '', $this->cacheExpire);
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
    public function setCacheGroup(string $newCacheKey): bool
    {
        //Create new addition
        $cacheObject = [$newCacheKey];

        //Get old cache
        $previousCachedObject = wp_cache_get($this->cacheGroup);
        if (is_array($previousCachedObject) && !empty($previousCachedObject)) {
            $cacheObject = array_merge($cacheObject, $previousCachedObject);
        }

        return wp_cache_set($this->cacheGroup, array_unique($cacheObject));
    }

    /**
     * Get from cache
     *
     * @param The cache key $key
     * @return mixed
     */
    public function getCache($key, $persistent = true): mixed
    {
        //Get runtime cache
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        //Get persistent cache, store runtime
        if ($persistent) {
            return $this->cache[$key] = wp_cache_get($key);
        }

        return null;
    }
}