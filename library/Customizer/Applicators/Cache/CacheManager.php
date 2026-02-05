<?php

namespace Municipio\Customizer\Applicators\Cache;

use Municipio\Customizer\Applicators\ApplicatorInterface;

/**
 * Manages the overall caching process, orchestrating cache operations
 */
class CacheManager implements CacheManagerInterface
{
    private static array $firstRunTracker = [];

    public function __construct(
        private CacheKeyGeneratorInterface $keyGenerator,
        private CacheStorageInterface $storage,
        private SignatureGeneratorInterface $signatureGenerator
    ) {
    }

    /**
     * Create and apply cache for applicators
     *
     * @param ApplicatorInterface ...$applicators The applicators to cache
     * @return void
     */
    public function createAndApplyCache(ApplicatorInterface ...$applicators): void
    {
        if (!$this->isFrontend()) {
            return;
        }

        if (!$this->firstRun(__METHOD__)) {
            return;
        }

        $cacheKey = $this->keyGenerator->generateCacheKey();
        $cachedData = $this->storage->retrieve($cacheKey);

        if (is_null($cachedData)) {
            $cachedData = $this->createCache($cacheKey, ...$applicators);
        }

        $this->applyCachedData($cachedData, ...$applicators);
    }

    /**
     * Apply cached data to applicators
     *
     * @param array $cachedData The cached data
     * @param ApplicatorInterface ...$applicators The applicators to apply data to
     * @return void
     */
    public function applyCachedData(array $cachedData, ApplicatorInterface ...$applicators): void
    {
        if (empty($cachedData)) {
            throw new \Exception('No cache found for customizer settings.');
        }

        foreach ($applicators as $applicator) {
            $applicatorData = $cachedData[$applicator->getKey()] ?? null;
            if (!is_null($applicatorData)) {
                $applicator->applyData($applicatorData);
            }
        }
    }

    /**
     * Clear all cache data
     *
     * @return bool True if cache was cleared, false otherwise
     */
    public function clearCache(): bool
    {
        return $this->storage->clear('theme_mod_applicator_cache');
    }

    /**
     * Clear object cache
     *
     * @return void
     */
    public function clearObjectCache(): void
    {
        $this->storage->clearObjectCache();
    }

    /**
     * Create cache for applicators
     *
     * @param string $cacheKey The cache key
     * @param ApplicatorInterface ...$applicators The applicators to cache
     * @return array The cached data
     */
    private function createCache(string $cacheKey, ApplicatorInterface ...$applicators): array
    {
        $cacheEntity = [];

        foreach ($applicators as $applicator) {
            $cacheEntity[$applicator->getKey()] = $applicator->getData();
        }

        $this->storage->store($cacheKey, $cacheEntity);

        return $cacheEntity;
    }

    /**
     * Check if the current request is a frontend request
     *
     * @return bool
     */
    private function isFrontend(): bool
    {
        return !is_admin() && !defined('WP_CLI') && !defined('WP_IMPORTING') && !defined('WP_INSTALLING');
    }

    /**
     * Ensure the method runs only on its first invocation
     *
     * @param string $key Unique key for the method
     * @return bool True if this is the first run, false otherwise
     */
    private function firstRun(string $key): bool
    {
        if (isset(self::$firstRunTracker[$key])) {
            return false;
        }
        self::$firstRunTracker[$key] = true;
        return true;
    }
}