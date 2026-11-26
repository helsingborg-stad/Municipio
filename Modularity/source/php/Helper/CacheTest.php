<?php

declare(strict_types=1);

namespace Modularity\Helper;

use Municipio\TestUtils\InMemoryObjectCache;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

function is_multisite(): bool
{
    return false;
}

function is_user_logged_in(): bool
{
    return false;
}

function add_action(string $hook, mixed $callback): void {}

function current_time(string $format, int $gmt = 0): string
{
    return '2026-09-28 12:00:00';
}

function wp_cache_get(int|string $key, string $group = ''): mixed
{
    return CacheTest::$objectCache->wpCacheGet($key, $group);
}

function wp_cache_delete(int|string $key, string $group = ''): bool
{
    return CacheTest::$objectCache->wpCacheDelete($key, $group);
}

function wp_cache_add(int|string $key, mixed $value, string $group = '', int $ttl = 0): bool
{
    return CacheTest::$objectCache->wpCacheAdd($key, $value, $group, $ttl);
}

class CacheTest extends TestCase
{
    public static InMemoryObjectCache $objectCache;

    protected function setUp(): void
    {
        self::$objectCache = new InMemoryObjectCache();
    }

    public function testCacheHitRestoresAssetsWithoutChangingMarkupEntryFormat(): void
    {
        $context = ['components' => [['slug' => 'collection', 'dependencies' => []]]];
        $assets = $this->createMock(FragmentAssetContextInterface::class);
        $assets->expects($this->exactly(2))->method('beginFragment');
        $assets->expects($this->exactly(2))->method('endFragment')->willReturn($context);
        $assets->expects($this->once())->method('restoreFragment')->with($context);

        ob_start();
        $cache = new Cache(42, 'module', 60, null, $assets);
        static::assertTrue($cache->start());
        echo '<div data-component="collection">Content</div>';
        static::assertTrue($cache->stop());
        ob_end_clean();

        $entries = self::$objectCache->getGroup('modules')[42];
        static::assertCount(2, $entries);
        static::assertSame($context, $entries[array_key_last($entries)]);
        static::assertIsString(reset($entries));

        ob_start();
        static::assertFalse((new Cache(42, 'module', 60, null, $assets))->start());
        $markup = ob_get_clean();
        static::assertStringContainsString('Content', $markup);

        // An HTML-only entry from the previous release is refreshed on first use.
        $key = array_key_last($entries);
        $entriesWithoutAssets = $entries;
        unset($entriesWithoutAssets[$key]);
        self::$objectCache->wpCacheSet(42, $entriesWithoutAssets, 'modules');
        ob_start();
        $legacy = new Cache(42, 'module', 60, null, $assets);
        static::assertTrue($legacy->start());
        echo '<div data-component="collection">Fresh content</div>';
        static::assertTrue($legacy->stop());
        static::assertStringContainsString('Fresh content', ob_get_clean());
        static::assertSame($context, self::$objectCache->getGroup('modules')[42][$key]);
    }

    /**
     * @see https://github.com/helsingborg-stad/Municipio/issues/2435
     */
    #[TestDox('stop() stores each fragment of a post in its own cache entry')]
    public function testStopStoresEachFragmentInItsOwnCacheEntry(): void
    {
        $this->storeFragment(42, 'module-a', 'Fragment A');
        $this->storeFragment(42, 'module-b', 'Fragment B');

        foreach (self::$objectCache->getGroup('modules') as $key => $value) {
            static::assertFalse(
                str_contains(serialize($value), 'Fragment A') && str_contains(serialize($value), 'Fragment B'),
                "Expected each fragment in its own cache entry, but entry \"{$key}\" holds both.",
            );
        }
    }

    /**
     * @see https://github.com/helsingborg-stad/Municipio/issues/2435
     */
    #[TestDox('stop() does not rewrite, or extend the lifetime of, other fragments of the post')]
    public function testStopDoesNotRewriteOtherFragmentsOfThePost(): void
    {
        $this->storeFragment(42, 'module-a', 'Fragment A');
        self::$objectCache->clearWrites();

        $this->storeFragment(42, 'module-b', 'Fragment B');

        foreach (self::$objectCache->getWrites() as $write) {
            static::assertStringNotContainsString(
                'Fragment A',
                serialize($write['data']),
                'Expected storing fragment B not to write fragment A again, which would reset its TTL.',
            );
        }
    }

    #[TestDox('start() serves a stored fragment from the cache')]
    public function testStartServesAStoredFragment(): void
    {
        $this->storeFragment(42, 'module-a', 'Fragment A');

        ob_start();
        $isMiss = (new Cache(42, 'module-a', 60))->start();
        $output = (string) ob_get_clean();

        static::assertFalse($isMiss);
        static::assertStringContainsString('Fragment A', $output);
    }

    #[TestDox('clearCache() makes every fragment of the post miss')]
    public function testClearCacheMakesEveryFragmentOfThePostMiss(): void
    {
        $this->storeFragment(42, 'module-a', 'Fragment A');
        $this->storeFragment(42, 'module-b', 'Fragment B');

        (new Cache(42, 'module-a', 60))->clearCache(42);

        static::assertTrue($this->isMiss(42, 'module-a'));
        static::assertTrue($this->isMiss(42, 'module-b'));
    }

    private function storeFragment(int $postId, string $module, string $markup): void
    {
        ob_start();
        $cache = new Cache($postId, $module, 60);
        $cache->start();
        echo $markup;
        $cache->stop();
        ob_end_clean();
    }

    private function isMiss(int $postId, string $module): bool
    {
        ob_start();
        $cache = new Cache($postId, $module, 60);
        $isMiss = $cache->start();
        if ($isMiss) {
            $cache->stop();
        }
        ob_end_clean();

        return $isMiss;
    }
}
