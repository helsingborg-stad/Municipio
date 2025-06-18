<?php

namespace Municipio\Cache\Implementations;

use Municipio\Cache\CacheInterface;
use WpService\Contracts\WpCacheDelete;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheSet;

/**
 * Class WpCache
 *
 * This class implements the CacheInterface using WordPress's caching functions.
 * It provides methods to get, set, and delete cache entries.
 *
 * @package Municipio\Cache\Implementations
 */
class WpCache implements CacheInterface
{
    public const DEFAULT_GROUP = "WpCacheDefaultGroup";

    /**
     * WpCache constructor.
     *
     * @param WpCacheSet|WpCacheGet|WpCacheDelete $wpService The WpService instance.
     */
    public function __construct(private WpCacheSet&WpCacheGet&WpCacheDelete $wpService)
    {
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, ?string $group = null): mixed
    {
        return $this->wpService->wpCacheGet($key, $group ?? self::DEFAULT_GROUP);
    }

    /**
     * @inheritDoc
     *
     * @param string $key The cache key.
     * @param mixed $value The value to cache.
     * @param string|null $group The cache group.
     * @param int|null $expire This is ignored in this implementation.
     *
     * @return void
     */
    public function set(string $key, mixed $value, ?string $group = null, ?int $expire = null): void
    {
        $this->wpService->wpCacheSet($key, $value, $group ?? self::DEFAULT_GROUP, $expire);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key, ?string $group = null): void
    {
        $this->wpService->wpCacheDelete($key, $group ?? self::DEFAULT_GROUP);
    }
}
