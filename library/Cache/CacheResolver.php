<?php

namespace Municipio\Cache;

use Municipio\Cache\Implementations\{StaticCache, WpCache};
use WpService\Contracts\{WpCacheGet, WpCacheDelete, WpCacheSet};

class CacheResolver
{
    public function __construct(private WpCacheSet&WpCacheGet&WpCacheDelete $wpService)
    {
    }

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
