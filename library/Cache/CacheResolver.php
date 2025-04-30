<?php

namespace Municipio\Cache;

use Municipio\Cache\Implementations\{StaticCache, WpCache};
use WpService\Contracts\{WpCacheGet, WpCacheDelete, WpCacheSet};

/**
 * Class CacheResolver
 *
 * This class resolves the appropriate cache implementation based on the environment.
 * It checks if WordPress caching is enabled and returns the corresponding cache instance.
 *
 * @package Municipio\Cache
 */
class CacheResolver
{
    /**
     * CacheResolver constructor.
     *
     * @param WpCacheSet|WpCacheGet|WpCacheDelete $wpService The WpService instance.
     */
    public function __construct(private WpCacheSet&WpCacheGet&WpCacheDelete $wpService)
    {
    }

    /**
     * Resolves the appropriate cache implementation.
     *
     * @return CacheInterface The resolved cache instance.
     */
    public function resolve(): CacheInterface
    {
        if ($this->canUseWpCache() === true) {
            return new WpCache($this->wpService);
        }

        return new StaticCache();
    }

    /**
     * @inheritDoc
     */
    private function canUseWpCache(): bool
    {
        return function_exists('wp_cache_get') && defined('WP_CACHE') && constant('WP_CACHE') === true;
    }
}
