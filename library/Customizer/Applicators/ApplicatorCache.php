<?php

namespace Municipio\Customizer\Applicators;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use wpdb;
use Kirki\Compatibility\Kirki as KirkiCompatibility;
use Kirki\Util\Helper as KirkiHelper;
use Municipio\Customizer\Applicators\ApplicatorInterface;

class ApplicatorCache implements Hookable, ApplicatorCacheInterface
{
    private $cacheKeyBaseName  = 'theme_mod_applicator_cache';
    private array $applicators = [];

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
        $this->wpService->addAction('kirki_dynamic_css', array($this, 'tryCreateCache'), 5);
        $this->wpService->addAction('kirki_dynamic_css', array($this, 'tryApplyCache'), 10);
        $this->wpService->addAction('customize_save_after', array($this, 'tryClearCache'), 20);
        $this->wpService->addAction('admin_init', array($this, 'tryClearCacheByUrl'), 20);
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
   *
   * @return bool True if the cache was cleared, false otherwise (no cache found).
   */
    public function tryClearCache(): bool
    {
        $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM {$this->wpdb->options} 
           WHERE option_name LIKE %s",
                $this->cacheKeyBaseName . '_%'
            )
        );
        $cacheCleared = (bool) $this->wpdb->rows_affected;

        if ($cacheCleared) {
            $this->wpService->doAction("Municipio/Customizer/CacheCleared");
        }
        return $cacheCleared;
    }

  /**
   * Run cache control.
   *
   * @return void
   */
    public function tryCreateCache()
    {
      //Check if in frontend
        if (!$this->isFrontend()) {
            return;
        }

      //Try to get the static cache
        $staticCache = $this->getStaticCache(
            $this->getCacheKey()
        );

        if (is_null($staticCache)) {
            $this->createStaticCache(
                $this->getCacheKey(),
                ...$this->applicators
            );
        }
    }

  /**
   * Apply cached data to frontend.
   *
   * @return void
   */
    public function tryApplyCache()
    {
      //Check if in frontend
        if (!$this->isFrontend()) {
            return;
        }

      //Try to get the static cache
        $staticCache = $this->getStaticCache(
            $this->getCacheKey()
        );

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
        return !is_admin() && !defined('DOING_AJAX') && !defined('REST_REQUEST') && !defined('WP_CLI') && !defined('WP_IMPORTING') && !defined('WP_INSTALLING');
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

        $this->wpService->doAction("Municipio/Customizer/LoadFields", $cacheKey);

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
            '%s_%s_%s_%s',
            $this->cacheKeyBaseName,
            $this->getCustomizerStateKey(),
            $this->getCustomizerLastPublished(),
            $this->getCustomzerFieldSignature() .
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

        return $latestDate ? strtotime($latestDate) : time();
    }

  /**
   * Get the signature of the customizer fields.
   *
   * @return string
   */
    private function getCustomzerFieldSignature(): string
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
}
