<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

interface FragmentStoreInterface
{
    public function get(int|string $postId, string $hash): ?Fragment;

    public function set(int|string $postId, string $hash, Fragment $fragment, int $ttl): void;

    /**
     * Makes every stored fragment of the post miss.
     */
    public function clear(int|string $postId): void;
}
