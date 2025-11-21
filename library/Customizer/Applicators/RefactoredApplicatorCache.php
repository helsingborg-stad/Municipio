<?php

namespace Municipio\Customizer\Applicators;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use Municipio\Customizer\Applicators\Cache\CacheManagerInterface;

/**
 * Refactored ApplicatorCache following SOLID principles
 * 
 * This class now follows Single Responsibility Principle by delegating 
 * cache operations to specialized cache components through dependency injection.
 */
class RefactoredApplicatorCache implements Hookable, ApplicatorCacheInterface
{
    private array $applicators = [];
    private static array $firstRunTracker = [];

    /**
     * Constructor with dependency injection
     */
    public function __construct(
        private WpService $wpService,
        private CacheManagerInterface $cacheManager,
        ApplicatorInterface ...$applicators
    ) {
        $this->applicators = $applicators;
    }

    /**
     * Add hooks for cache management
     *
     * @return void
     */
    public function addHooks(): void
    {
        // Create & apply cache
        $this->wpService->addAction('kirki_dynamic_css', [$this, 'tryCreateAndApplyCache'], 5);
        $this->wpService->addAction('rest_api_init', [$this, 'tryCreateAndApplyCache'], 5);

        // Clear cache when customizer is saved
        $this->wpService->addAction('customize_save_after', [$this, 'tryClearCache'], 20);
        $this->wpService->addAction('customize_save_after', [$this, 'tryClearObjectCache'], 25);

        // Allow clearing cache by URL if user can customize
        $this->wpService->addAction('admin_init', [$this, 'tryClearCacheByUrl'], 20);

        // Disable object cache in runtime when in customizer & preview
        $this->wpService->addAction('customize_controls_enqueue_scripts', [$this, 'disableObjectCacheInRuntime'], 1);
        $this->wpService->addAction('customize_preview_init', [$this, 'disableObjectCacheInRuntime'], 1);
    }

    /**
     * Try to create and apply cache
     *
     * @return void
     */
    public function tryCreateAndApplyCache(): void
    {
        $this->cacheManager->createAndApplyCache(...$this->applicators);
    }

    /**
     * Clear cache manually via URL parameter
     *
     * @return void
     */
    public function tryClearCacheByUrl(): void
    {
        if (isset($_GET['clear_customizer_cache']) && $this->wpService->currentUserCan('customize')) {
            $this->tryClearCache();
        }
    }

    /**
     * Clear the cache
     *
     * @return bool True if the cache was cleared, false otherwise
     */
    public function tryClearCache(): bool
    {
        return $this->cacheManager->clearCache();
    }

    /**
     * Clear the WordPress object cache
     *
     * @return void
     */
    public function tryClearObjectCache(): void
    {
        $this->cacheManager->clearObjectCache();
    }

    /**
     * Disable object cache in runtime for customizer
     *
     * @return void
     */
    public function disableObjectCacheInRuntime(): void
    {
        if (!$this->firstRun(__METHOD__)) {
            return;
        }

        // TODO: WpService should be updated to support this
        // Using direct call as a temporary workaround until WpService provides this method
        wp_using_ext_object_cache(false);
        $this->wpService->wpCacheFlush();
        $this->wpService->wpCacheInit();
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