<?php

namespace Municipio\Controller\Navigation\Cache;

class NavigationRuntimeCache
{
    //Static cache for ancestors
    private static $runtimeCache = [
        'ancestors'         => [
            [
                'toplevel'   => [],
                'notoplevel' => []
            ]
        ],
        'complementObjects' => []
    ];

    /**
     * Store in cache
     *
     * @param string $key   The key to store in
     * @param mixed $data   The value to store
     */
    public static function setCache(string $key, mixed $data): void
    {
        self::$runtimeCache[$key] = $data;
    }

    /**
     * Get from cache
     *
     * @param string $key   The cache key
     * @param array|null 
     * @return mixed
     */
    public static function getCache(string $key): array|null
    {
        if (isset(self::$runtimeCache[$key])) {
            return self::$runtimeCache[$key];
        }

        return null;
    }
}