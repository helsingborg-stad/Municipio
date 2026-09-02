<?php

declare(strict_types=1);

namespace Municipio\Content;

use PHPUnit\Framework\TestCase;

class FilteredContentRuntimeCacheTest extends TestCase
{
    protected function setUp(): void
    {
        FilteredContentRuntimeCache::clear();
    }

    public function testItFiltersIdenticalContentOnceInTheSameContext(): void
    {
        $calls  = 0;
        $filter = static function (string $content) use (&$calls): string {
            $calls++;
            return $content . '-filtered-' . $calls;
        };

        $first  = FilteredContentRuntimeCache::remember(1, 10, 'content', $filter);
        $second = FilteredContentRuntimeCache::remember(1, 10, 'content', $filter);

        static::assertSame('content-filtered-1', $first);
        static::assertSame($first, $second);
        static::assertSame(1, $calls);
    }

    public function testItSeparatesSitesPostsAndContent(): void
    {
        $calls  = 0;
        $filter = static function (string $content) use (&$calls): string {
            $calls++;
            return $content;
        };

        FilteredContentRuntimeCache::remember(1, 10, 'content', $filter);
        FilteredContentRuntimeCache::remember(2, 10, 'content', $filter);
        FilteredContentRuntimeCache::remember(1, 11, 'content', $filter);
        FilteredContentRuntimeCache::remember(1, 10, 'other content', $filter);

        static::assertSame(4, $calls);
    }
}
