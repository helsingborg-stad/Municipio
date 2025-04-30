<?php

namespace Municipio\Cache\Implementations;

use Municipio\Cache\CacheInterface;

/**
 * NullCache is a cache implementation that does nothing.
 *
 * This class is useful for testing purposes or when you want to disable caching
 * without removing the cache-related code.
 */
class NullCache implements CacheInterface
{
    /**
     * @inheritDoc
     */
    public function get(string $key, ?string $group = null): mixed
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, ?string $group = null, ?int $expire = null): void
    {
        return;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key, ?string $group = null): void
    {
        return;
    }
}
