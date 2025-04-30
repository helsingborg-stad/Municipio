<?php

namespace Municipio\Cache\MethodCache;

interface MethodCacheInterface
{
    /**
     * Caches a function call
     *
     * @param callable $callable  The function call to cache.
     * @param array $args to function call.
     */
    public function cache(callable $callable, array $args, ?int $expire = null);
}
