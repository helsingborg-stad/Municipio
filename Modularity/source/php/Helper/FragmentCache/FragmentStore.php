<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

use WpService\Contracts\WpCacheAdd;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheIncr;
use WpService\Contracts\WpCacheSet;

/**
 * Stores each fragment under its own object cache key, with its own TTL.
 *
 * Keys include a per-post version. Clearing a post bumps the version, so its old
 * fragments can no longer be reached and expire through their TTL.
 */
final class FragmentStore implements FragmentStoreInterface
{
    public function __construct(
        private readonly WpCacheGet&WpCacheSet&WpCacheAdd&WpCacheIncr $objectCache,
        private readonly string $group,
    ) {}

    public function get(int|string $postId, string $hash): ?Fragment
    {
        return Fragment::fromArray($this->objectCache->wpCacheGet($this->getFragmentKey($postId, $hash), $this->group));
    }

    public function set(int|string $postId, string $hash, Fragment $fragment, int $ttl): void
    {
        $this->objectCache->wpCacheSet($this->getFragmentKey($postId, $hash), $fragment->toArray(), $this->group, $ttl);
    }

    public function clear(int|string $postId): void
    {
        if ($this->objectCache->wpCacheIncr($this->getVersionKey($postId), 1, $this->group) === false) {
            $this->objectCache->wpCacheSet($this->getVersionKey($postId), $this->createVersion(), $this->group);
        }
    }

    private function getFragmentKey(int|string $postId, string $hash): string
    {
        return $postId . ':' . $this->getVersion($postId) . ':' . $hash;
    }

    private function getVersion(int|string $postId): int
    {
        $version = $this->objectCache->wpCacheGet($this->getVersionKey($postId), $this->group);

        if (is_int($version)) {
            return $version;
        }

        // Add, not set: a concurrent request may have created the version first.
        $this->objectCache->wpCacheAdd($this->getVersionKey($postId), $this->createVersion(), $this->group);

        return (int) $this->objectCache->wpCacheGet($this->getVersionKey($postId), $this->group);
    }

    private function getVersionKey(int|string $postId): string
    {
        return $postId . ':version';
    }

    /**
     * A random start version, so a version key that was evicted never comes back with an old value.
     */
    private function createVersion(): int
    {
        return random_int(1, PHP_INT_MAX >> 1);
    }
}
