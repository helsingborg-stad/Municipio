<?php

namespace Municipio\Customizer\Applicators\Cache;

use Municipio\Customizer\Applicators\ApplicatorInterface;

/**
 * Interface for managing the overall caching process
 */
interface CacheManagerInterface
{
    /**
     * Create and apply cache for applicators
     *
     * @param ApplicatorInterface ...$applicators The applicators to cache
     * @return void
     */
    public function createAndApplyCache(ApplicatorInterface ...$applicators): void;

    /**
     * Apply cached data to applicators
     *
     * @param array $cachedData The cached data
     * @param ApplicatorInterface ...$applicators The applicators to apply data to
     * @return void
     */
    public function applyCachedData(array $cachedData, ApplicatorInterface ...$applicators): void;

    /**
     * Clear all cache data
     *
     * @return bool True if cache was cleared, false otherwise
     */
    public function clearCache(): bool;
}