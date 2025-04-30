<?php

namespace Municipio\Cache;

/**
 * GlobalCache class
 *
 * This class provides a static interface to a cache instance.
 * It allows setting and getting a cache instance globally.
 */
class GlobalCache implements GlobalCacheInterface
{
    private static ?CacheInterface $cache = null;

    /**
     * @inheritDoc
     */
    public static function setCache(CacheInterface $cache): void
    {
        self::$cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public static function getCache(): CacheInterface
    {
        if (self::$cache === null) {
            throw new \RuntimeException('Cache instance is not set.');
        }

        return self::$cache;
    }
}
