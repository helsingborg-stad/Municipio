<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

interface FragmentStoreInterface
{
    /**
     * Resolves the key of a fragment for the current version of the post.
     */
    public function getKey(int|string $postId, string $hash): FragmentKey;

    public function get(FragmentKey $key): ?Fragment;

    public function set(FragmentKey $key, Fragment $fragment, int $ttl): void;

    /**
     * Makes every stored fragment of the post miss.
     */
    public function clear(int|string $postId): void;
}
