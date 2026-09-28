<?php

declare(strict_types=1);

namespace Modularity\Helper;

use PHPUnit\Framework\TestCase;

function is_multisite(): bool
{
    return false;
}

function is_user_logged_in(): bool
{
    return false;
}

function add_action(string $hook, mixed $callback): void
{
}

function current_time(string $format, int $gmt = 0): string
{
    return '2026-09-28 12:00:00';
}

function wp_cache_get(mixed $key, string $group): mixed
{
    return CacheTest::get($key, $group);
}

function wp_cache_delete(mixed $key, string $group): bool
{
    return CacheTest::delete($key, $group);
}

function wp_cache_add(mixed $key, mixed $value, string $group, int $ttl): bool
{
    return CacheTest::add($key, $value, $group);
}

class CacheTest extends TestCase
{
    private static array $storage = [];

    protected function setUp(): void
    {
        self::$storage = [];
    }

    public static function get(mixed $key, string $group): mixed
    {
        return self::$storage[$group][$key] ?? false;
    }

    public static function delete(mixed $key, string $group): bool
    {
        unset(self::$storage[$group][$key]);
        return true;
    }

    public static function add(mixed $key, mixed $value, string $group): bool
    {
        self::$storage[$group][$key] = $value;
        return true;
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

        $entries = self::$storage['modules'][42];
        static::assertCount(2, $entries);
        static::assertSame($context, $entries[array_key_last($entries)]);
        static::assertIsString(reset($entries));

        ob_start();
        static::assertFalse((new Cache(42, 'module', 60, null, $assets))->start());
        $markup = ob_get_clean();
        static::assertStringContainsString('Content', $markup);

        // An HTML-only entry from the previous release is refreshed on first use.
        $key = array_key_last($entries);
        unset(self::$storage['modules'][42][$key]);
        ob_start();
        $legacy = new Cache(42, 'module', 60, null, $assets);
        static::assertTrue($legacy->start());
        echo '<div data-component="collection">Fresh content</div>';
        static::assertTrue($legacy->stop());
        static::assertStringContainsString('Fresh content', ob_get_clean());
        static::assertSame($context, self::$storage['modules'][42][$key]);
    }
}
