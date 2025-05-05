<?php

namespace Municipio\Cache\MethodCache;

interface MethodCacheInterface
{
    /**
     * Caches a function call
     *
     * @param callable  $callable   The function call to cache.
     * @param array     $arguments  Argument sent function call.
     * @param int|null  $expire     Expiration time in seconds or null for no expiration.
     * @param array     $useGlobalState Use global state for cache key. Can be true, false or an array of properties in $GLOBAL to use.
     *
     * @return mixed The result of the function call.
     */
    public function cache(callable $callable, array $args, ?int $expire = null, bool|array $useGlobalState = false): mixed;
}
