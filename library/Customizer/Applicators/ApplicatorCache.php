<?php

namespace Municipio\Customizer\Applicators;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use wpdb;
use Kirki\Compatibility\Kirki as KirkiCompatibility;
use Municipio\Customizer\Applicators\ApplicatorInterface;

/**
 * Class ApplicatorCache
 *
 * Handles caching for the customizer applicators.
 */
class ApplicatorCache implements Hookable, ApplicatorCacheInterface
{
    private string $cacheKeyBaseName = 'theme_mod_applicator_cache';
    private array $applicators       = [];
    private static $firstRunTracker  = [];

    /**
     * Constructor.
     */
    public function __construct(private WpService $wpService, private wpdb $wpdb, ApplicatorInterface ...$applicators)
    {
        $this->applicators = $applicators;
    }

  /**
   * Add hooks.
   *
   * @return void
   */
    public function addHooks(): void
    {
        //Create & apply cache.
        $this->wpService->addAction('kirki_dynamic_css', array($this, 'tryCreateAndApplyCache'), 5);

        //Create & apply cache on rest requests.
        $this->wpService->addAction('rest_api_init', array($this, 'tryCreateAndApplyCache'), 5);

        //Clear cache when customizer is saved (static option cache, and object cache).
        $this->wpService->addAction('customize_save_after', array($this, 'tryClearCache'), 20);
        $this->wpService->addAction('customize_save_after', array($this, 'tryClearObjectCache'), 25);

        //Allow to clear cache by url, if user can customize.
        $this->wpService->addAction('admin_init', array($this, 'tryClearCacheByUrl'), 20);

        // Disable object cache in runtime, when in customizer & preview.
        $this->wpService->addAction('customize_controls_enqueue_scripts', array($this, 'disableObjectCacheInRuntime'), 1);
        $this->wpService->addAction('customize_preview_init', array($this, 'disableObjectCacheInRuntime'), 1);
    }

  /**
   * Manually clear the cache.
   *
   * @return void
   */
    public function tryClearCacheByUrl()
    {
        if (isset($_GET['clear_customizer_cache'])) {
            if ($this->wpService->currentUserCan('customize')) {
                $this->tryClearCache();
            }
        }
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
        $matchingOptions = $this->wpdb->get_col(
            "SELECT option_name 
            FROM {$this->wpdb->options} 
            WHERE option_name LIKE '{$this->cacheKeyBaseName}_%'"
        );
        $cacheCleared    = false;
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
     * Clear the WordPress cache.
     *
     * @return void
     */
    public function tryClearObjectCache(): void
    {
        $this->wpService->wpCacheFlush();
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
        if (!$this->firstRun(__METHOD__)) {
            return;
        }
        //TODO: WpService should be updated to support this. $this->wpService->wpUsingExtObjectCache(false); null return not allowed.
        wp_using_ext_object_cache(false);
        $this->wpService->wpCacheFlush();
        $this->wpService->wpCacheInit();
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

        $cacheKey = $this->getCacheKey();

        $staticCache = $this->getStaticCache(
            $cacheKey
        );

        if (is_null($staticCache)) {
            $staticCache = $this->createStaticCache(
                $cacheKey,
                ...$this->applicators
            );
        }

        $this->tryApplyCache($cacheKey, $staticCache);
    }

  /**
   * Apply cached data to frontend.
   *
   * @return void
   */
    private function tryApplyCache(?string $cacheKey = null, ?array $staticCache = null): void
    {
        if (is_null($staticCache)) {
            $staticCache = $this->getStaticCache(
                ($cacheKey ?: $this->getCacheKey())
            );
        }

        if (is_null($staticCache)) {
            throw new \Exception('No cache found for customizer settings.');
        }

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
   * @return void
   */
    public function createStaticCache(string $cacheKey, ApplicatorInterface ...$applicators): array
    {
        $cacheEntity = [];

        foreach ($applicators as $applicator) {
            $cacheEntity[$applicator->getKey()] = $applicator->getData();
        }

        $this->storeCache($cacheKey, $cacheEntity);

        return $cacheEntity;
    }

  /**
   * Get the static cache identifier.
   *
   * @return string
   */
    private function getCacheKey(): string
    {
        return sprintf(
            '%s_%s_%s_%s%s',
            $this->cacheKeyBaseName,
            $this->getCustomizerStateKey(),
            $this->getCustomizerLastPublished(),
            $this->getCustomizerFieldSignature(),
            $this->getCacheKeySuffix()
        );
    }

  /**
   * Store the cache.
   *
   * @param array $cacheEntity The cache entity to store.
   *
   * @return void
   */
    private function storeCache($cacheKey, $cacheEntity): void
    {
        $this->wpService->addOption(
            $cacheKey,
            $cacheEntity
        );
    }

  /**
   * Get the static cache.
   *
   * @param string $cacheKey The cache key to get the cache for.
   *
   * @return array|null
   */
    public function getStaticCache(string $cacheKey): array|null
    {
        $staticCache = $this->wpService->getOption(
            $cacheKey
        ) ?: null;
        return $this->wpService->applyFilters('Municipio/Customizer/StaticCache', $staticCache);
    }

  /**
   * Get the last published date of the customizer.
   *
   * @return string|null
   */
    private function getCustomizerLastPublished(): string|null
    {
        $postStatus = $this->getCustomizerStateKey() === 'draft' ?
        ['draft', 'auto-draft', 'inherit', 'future', 'trash', 'publish'] :
        ['publish'];

        $latestDate = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT post_modified_gmt 
          FROM {$this->wpdb->posts} 
          WHERE post_type = %s 
          AND post_status IN (%s) 
          ORDER BY post_modified_gmt DESC 
          LIMIT 1",
                'customize_changeset',
                implode(",", $postStatus)
            )
        );

        return $latestDate ? strtotime($latestDate) : 'unknown';
    }

  /**
   * Get the signature of the customizer fields.
   *
   * @return string
   */
    private function getCustomizerFieldSignature(): string
    {
        $fields = [];
        if (class_exists('\Kirki\Compatibility\Kirki')) {
            $fields = array_merge(
                KirkiCompatibility::$fields ?? [],
                KirkiCompatibility::$all_fields ?? [],
                $fields
            );
        }
        return $this->getArraySignature($fields);
    }

  /**
   * Get the customizer state key.
   * Determines if the customizer is in preview mode or not.
   *
   * @return string
   */
    private function getCustomizerStateKey(): string
    {
        return $this->wpService->isCustomizePreview() ? 'draft' : 'publish';
    }

  /**
   * Get the cache key suffix.
   * This may be used when a postType needs to be cached separately.
   *
   * @return string
   */
    private function getCacheKeySuffix(): string
    {
        return $this->wpService->applyFilters(
            'Municipio/Customizer/CacheKeySuffix',
            '',
            $this->wpService->isCustomizePreview(),
            $this->wpService->getPostType()
        );
    }

  /**
   * Create a signature for the given data.
   *
   * @param array $data The data to create a signature for.
   *
   * @return string
   */
    protected function getArraySignature(array $data): string
    {
        $supportedHashes = hash_algos() ?? [];
        if (in_array('xxh3', $supportedHashes)) {
            $hash = hash('sha256', json_encode($data));
        }
        $hash = hash('md5', json_encode($data));

        return $this->shortenHash($hash);
    }

  /**
   * Shorten hash.
   *
   * @param string $hash The hash to shorten.
   *
   * @return string
   */
    protected function shortenHash($hash): string
    {
        return substr($hash, 0, 8);
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
