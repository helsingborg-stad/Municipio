<?php

namespace Municipio\Cache;

interface CacheInterface
{
    /**
     * Get a value from the cache.
     *
     * @param string $key The cache key.
     * @param string|null $group The cache group.
     *
     * @return mixed The cached value or null if not found.
     */
    public function get(string $key, ?string $group = null): mixed;

    /**
     * Set a value in the cache.
     *
     * @param string $key The cache key.
     * @param mixed $value The value to cache.
     * @param string|null $group The cache group.
     * @param int|null $expire The expiration time in seconds.
     *
     * @return void
     */
    public function set(string $key, mixed $value, ?string $group = null, ?int $expire = null): void;

    /**
     * Delete a value from cache.
     *
     * @param string $key The cache key.
     * @param string|null $group The cache group.
     */
    public function delete(string $key, ?string $group = null): void;
}
