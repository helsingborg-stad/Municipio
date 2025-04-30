<?php

namespace Municipio\Cache;

interface GlobalCacheInterface
{
    /**
     * Get the cache instance.
     *
     * @return CacheInterface The cache instance.
     */
    public static function getCache(): CacheInterface;

    /**
     * Set the cache instance.
     *
     * @param CacheInterface $cache The cache instance.
     *
     * @return void
     */
    public static function setCache(CacheInterface $cache): void;
}
