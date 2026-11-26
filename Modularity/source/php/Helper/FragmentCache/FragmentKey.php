<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

/**
 * The object cache key of one fragment, including the version of its post at the time it was resolved.
 *
 * Resolve it once, on the cache miss, and write the rendered fragment to that same key.
 * If the post is cleared while the fragment renders, the write lands under the old
 * version, which is no longer read.
 */
final class FragmentKey
{
    public function __construct(
        public readonly string $value,
    ) {}
}
