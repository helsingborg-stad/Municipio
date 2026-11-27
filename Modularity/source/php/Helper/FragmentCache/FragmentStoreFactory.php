<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\IsMultisite;
use WpService\Contracts\WpCacheAdd;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheIncr;
use WpService\Contracts\WpCacheSet;

/**
 * Creates the fragment store for the current site.
 */
final class FragmentStoreFactory
{
    private const GROUP = 'modules';

    public function __construct(
        private readonly WpCacheGet&WpCacheSet&WpCacheAdd&WpCacheIncr $objectCache,
        private readonly IsMultisite&GetCurrentBlogId $site,
    ) {}

    public function create(): FragmentStoreInterface
    {
        return new FragmentStore($this->objectCache, $this->getGroup());
    }

    private function getGroup(): string
    {
        if (!$this->site->isMultisite()) {
            return self::GROUP;
        }

        return self::GROUP . '-' . $this->site->getCurrentBlogId();
    }
}
