<?php

namespace Municipio\Cache\Implementations;

use Municipio\Cache\CacheInterface;

class StaticCache implements CacheInterface
{
    private static $cache      = [];
    public const DEFAULT_GROUP = "StaticCacheDefaultGroup";

    /**
     * @inheritDoc
     */
    public function get(string $key, ?string $group = null): mixed
    {
        return self::$cache[$group ?? self::DEFAULT_GROUP][$key] ?? null;
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
        if (!isset(self::$cache[$group ?? self::DEFAULT_GROUP])) {
            self::$cache[$group ?? self::DEFAULT_GROUP] = [];
        }

        self::$cache[$group ?? self::DEFAULT_GROUP][$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key, ?string $group = null): void
    {
        unset(self::$cache[$group ?? self::DEFAULT_GROUP][$key]);

        if (empty(self::$cache[$group ?? self::DEFAULT_GROUP])) {
            unset(self::$cache[$group ?? self::DEFAULT_GROUP]);
        }
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        self::$cache = [];
    }
}
