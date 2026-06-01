<?php

namespace Municipio\Customizer\Applicators;

use Municipio\Customizer\Applicators\ApplicatorInterface;
use Municipio\HooksRegistrar\Hookable;
use wpdb;
use WpService\WpService;

/**
 * Class ApplicatorCache
 *
 * Handles caching for the customizer applicators.
 */
class ApplicatorCache implements Hookable, ApplicatorCacheInterface
{
    private array $applicators = [];
    private static $firstRunTracker = [];

    /**
     * Constructor.
     */
    public function __construct(
        private WpService $wpService,
        private wpdb $wpdb,
        ApplicatorInterface ...$applicators,
    ) {
        $this->applicators = $applicators;
    }

    /**
     * Add hooks.
     *
     * @return void
     */
    public function addHooks(): void
    {
        // Apply customizer outputs directly.
        $this->wpService->addAction('kirki_dynamic_css', array($this, 'tryCreateAndApplyCache'), 5);

        // Apply customizer outputs for REST requests.
        $this->wpService->addAction('rest_api_init', array($this, 'tryCreateAndApplyCache'), 5);
    }

    /**
     * Manually clear the cache.
     *
     * @return void
     */
    public function tryClearCacheByUrl()
    {
        return;
    }

    /**
     * Clear the cache.
     * This is designed intentionally, to use delete_option instead
     * of using delete statement. This is to avoid any potential
     * issues with cache plugins.
     *
     * @return bool True if the cache was cleared, false otherwise (no cache found).
     */
    public function tryClearCache(): bool
    {
        return false;
    }

    /**
     * Clear the WordPress cache.
     *
     * @return void
     */
    public function tryClearObjectCache(): void
    {
        return;
    }

    /**
     * Disable object cache in runtime.
     *
     * Used to bypass all persistent caches.
     *
     * @return void
     */
    public function disableObjectCacheInRuntime(): void
    {
        return;
    }

    /**
     * Run cache control.
     *
     * @return void
     */
    public function tryCreateAndApplyCache()
    {
        if (!$this->isFrontend()) {
            return;
        }

        if (!$this->firstRun(__METHOD__)) {
            return;
        }

        $this->tryApplyCache($this->createStaticCache('', ...$this->applicators));
    }

    /**
     * Apply cached data to frontend.
     *
     * @return void
     */
    private function tryApplyCache(array $staticCache): void
    {
        foreach ($this->applicators as $applicator) {
            $cachedData = $staticCache[$applicator->getKey()] ?? null;
            if (is_null($cachedData)) {
                continue;
            }
            $applicator->applyData($cachedData);
        }
    }

    /**
     * Check if the current request is a frontend request.
     *
     * @return bool
     */
    private function isFrontend(): bool
    {
        return !is_admin() && !defined('WP_CLI') && !defined('WP_IMPORTING') && !defined('WP_INSTALLING');
    }

    /**
     * Create a static cache.
     *
     * @param string $publishedTime The last published time.
     * @param string $fieldSignature The field signature.
     *
     * @return array<string, array|object|string>
     */
    public function createStaticCache(string $cacheKey, ApplicatorInterface ...$applicators): array
    {
        $cacheEntity = [];

        foreach ($applicators as $applicator) {
            $cacheEntity[$applicator->getKey()] = $applicator->getData();
        }

        return $cacheEntity;
    }

    /**
     * Get the static cache.
     *
     * @param string $cacheKey The cache key to get the cache for.
     *
     * @return array|null
     */
    public function getStaticCache(string $cacheKey): ?array
    {
        return null;
    }

    /**
     * Ensure the method runs only on its first invocation.
     *
     * @param string $key Unique key for the method.
     * @return bool True if this is the first run, false otherwise.
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
