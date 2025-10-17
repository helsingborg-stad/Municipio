<?php

namespace Municipio\Customizer\Applicators\Cache;

use wpdb;
use WpService\WpService;

/**
 * Handles cache storage operations using WordPress options and object cache
 */
class CacheStorage implements CacheStorageInterface
{
    private string $cacheKeyBaseName = 'theme_mod_applicator_cache';

    public function __construct(
        private WpService $wpService,
        private wpdb $wpdb
    ) {
    }

    /**
     * Store cache data
     *
     * @param string $key The cache key
     * @param array $data The data to cache
     * @return bool True if stored successfully, false otherwise
     */
    public function store(string $key, array $data): bool
    {
        return $this->wpService->addOption($key, $data);
    }

    /**
     * Retrieve cache data
     *
     * @param string $key The cache key
     * @return array|null The cached data or null if not found
     */
    public function retrieve(string $key): array|null
    {
        $staticCache = $this->wpService->getOption($key) ?: null;
        return $this->wpService->applyFilters('Municipio/Customizer/StaticCache', $staticCache);
    }

    /**
     * Clear cache data
     *
     * @param string $keyPattern The cache key pattern to clear
     * @return bool True if cleared successfully, false otherwise
     */
    public function clear(string $keyPattern): bool
    {
        $matchingOptions = $this->wpdb->get_col(
            "SELECT option_name 
            FROM {$this->wpdb->options} 
            WHERE option_name LIKE '{$keyPattern}_%'"
        );

        $cacheCleared = false;
        foreach ($matchingOptions as $optionName) {
            if ($this->wpService->deleteOption($optionName)) {
                $cacheCleared = true;
            }
        }

        if ($cacheCleared) {
            $this->wpService->doAction("Municipio/Customizer/CacheCleared");
        }

        return $cacheCleared;
    }

    /**
     * Clear all object cache
     *
     * @return void
     */
    public function clearObjectCache(): void
    {
        $this->wpService->wpCacheFlush();
    }
}