<?php

namespace Municipio\Controller\Navigation\Cache;

class RuntimeCache
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
     * @param bool   $persistent If true, store persistently (ignored here since it's a runtime cache)
     * @return bool
     */
    public function setCache(string $key, mixed $data, bool $persistent = true): bool
    {
        self::$runtimeCache[$key] = $data;
        return true; // Always true since it's runtime cache
    }

    /**
     * Get from cache
     *
     * @param string $key   The cache key
     * @param bool   $persistent If true, get persistent cache (ignored here)
     * @return mixed
     */
    public function getCache(string $key, bool $persistent = true): mixed
    {
        if (isset(self::$runtimeCache[$key])) {
            return self::$runtimeCache[$key];
        }

        return null;
    }
}