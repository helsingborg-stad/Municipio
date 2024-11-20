<?php 


namespace Municipio\Customizer\Applicators;

use WpService\WpService;
use Municipio\HooksRegistrar\Hookable;
use wpdb;
use Kirki\Compatibility\Kirki as KirkiCompatibility;
use Kirki\Util\Helper as KirkiHelper;
use Municipio\Customizer\Applicators\ApplicatorInterface;

class ApplicatorCache implements Hookable {

  private $cacheKeyBaseName = 'theme_mod_applicator_cache';
  private array $applicators = [];

  public function __construct(private WpService $wpService, private wpdb $wpdb, ApplicatorInterface ...$applicators) {
    $this->applicators = $applicators;
  }

  public function addHooks(): void
  {
    $this->wpService->addAction('init', array($this, 'tryCreateCache'), 10);
    $this->wpService->addAction('init', array($this, 'tryApplyCache'), 20);
  }

  /**
   * Run cache control.
   * 
   * @return void
   */
  public function tryCreateCache() 
  {
    //Check if in frontend
    if(!$this->isFrontend()) {
      return;
    }

    //Try to get the static cache
    $staticCache = $this->getStaticCache(
      $this->getCacheKey()
    );

    if(is_null($staticCache)) {
      $this->createStaticCache(
        $this->getCacheKey(),
        ...$this->applicators
      );
    }

    die;
  }

  /** 
   * Apply cached data to frontend. 
   * 
   * @return void
   */
  public function tryApplyCache()
  {
    //Check if in frontend
    if(!$this->isFrontend()) {
      return;
    }

    //Try to get the static cache
    $staticCache = $this->getStaticCache(
      $this->getCacheKey()
    );

    if(is_null($staticCache)) {
      return;
    }

    foreach($this->applicators as $applicator) {
      $cachedData = $staticCache[$applicator->getKey()] ?? null;
      if(is_null($cachedData)) {
        continue;
      }
      $applicator->applyData();
    }
  }

  /**
   * Check if the current request is a frontend request.
   * 
   * @return bool
   */
  private function isFrontend(): bool
  {
    return !is_admin() && !is_customize_preview() && !defined('DOING_AJAX') && !defined('REST_REQUEST') && !defined('WP_CLI') && !defined('WP_IMPORTING') && !defined('WP_INSTALLING');
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

    foreach($applicators as $applicator) {
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
      '%s_%s_%s', 
      $this->cacheKeyBaseName, 
      $this->getCustomizerLastPublished(), 
      $this->getCustomzerFieldSignature()
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
    return $this->wpService->getOption(
      $cacheKey
    ) ?: null;
  }

  /**
   * Get the last published date of the customizer.
   * 
   * @return string|null
   */
  private function getCustomizerLastPublished(): string|null
  {
      $latestDate = $this->wpdb->get_var(
        $this->wpdb->prepare(
            "SELECT post_modified_gmt 
            FROM {$this->wpdb->posts} 
            WHERE post_type = %s 
              AND post_status = %s 
            ORDER BY post_modified_gmt DESC 
            LIMIT 1",
            'customize_changeset',
            'publish'
        )
      );
      return strtotime($latestDate) ?? null;
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