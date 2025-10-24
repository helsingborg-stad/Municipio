<?php

namespace Municipio\Customizer\Applicators\Cache;

/**
 * Interface for cache storage operations
 */
interface CacheStorageInterface
{
    /**
     * Store cache data
     *
     * @param string $key The cache key
     * @param array $data The data to cache
     * @return bool True if stored successfully, false otherwise
     */
    public function store(string $key, array $data): bool;

    /**
     * Retrieve cache data
     *
     * @param string $key The cache key
     * @return array|null The cached data or null if not found
     */
    public function retrieve(string $key): array|null;

    /**
     * Clear cache data
     *
     * @param string $keyPattern The cache key pattern to clear
     * @return bool True if cleared successfully, false otherwise
     */
    public function clear(string $keyPattern): bool;

    /**
     * Clear all object cache
     *
     * @return void
     */
    public function clearObjectCache(): void;
}