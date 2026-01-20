<?php

namespace Municipio\Integrations\Litespeed\Cache;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\IsUserLoggedIn;

/**
 * Class to add Pressidium consent vary header for LiteSpeed Cache.
 */
class PressidiumConsentVary implements Hookable
{

  private const COOKIE_NAME = 'pressidium_cookie_consent';

  public function __construct(public AddFilter&IsUserLoggedIn $wpService){}

  /**
   * Register hooks for adding pressidium consent vary header.
   *
   * @return void
   */
  public function addHooks(): void
  {
    $this->wpService->addFilter(
      'litespeed_cache_vary_headers',
      [$this, 'addPressidiumConsentVaryHeader']
    );
  }

  /**
   * Add user group to vary headers for logged in users.
   * This ensures that different cached versions are served
   * based on user groups.
   *
   * @param array $varyHeaders
   * @return array
   */
  public function addPressidiumConsentVaryHeader(array $varyHeaders): array
  {
      if (!empty($_COOKIE[self::COOKIE_NAME])) {
          $decoded = json_decode(stripslashes($_COOKIE[self::COOKIE_NAME]), true);
          if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
              $varyHeaders['pressidium_consent']  = $this->reduceConsentDataToVaryString($decoded);
          }
      }
      return $varyHeaders;
  }

  /**
   * Reduce consent data to a concise string for vary header.
   *
   * @param array $consentData
   * @return string
   */
  private function reduceConsentDataToVaryString(array $consentData): string
  {
      $categories = $consentData['categories'] ?? [];
      $categories = is_array($categories) ? $categories : [];
      sort($categories);

      $levels = $consentData['level'] ?? [];
      $levels = is_array($levels) ? $levels : [];
      sort($levels);

      $reduced = [
          'categories' => $categories,
          'level'      => $levels,
      ];
      return md5(json_encode($reduced));
  }
}