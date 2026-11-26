<?php

declare(strict_types=1);

namespace Modularity\Helper;

use Modularity\Helper\FragmentCache\FragmentStore;
use Municipio\TestUtils\InMemoryObjectCache;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

function is_user_logged_in(): bool
{
    return false;
}

function current_time(string $format, int $gmt = 0): string
{
    return '2026-09-28 12:00:00';
}

class CacheTest extends TestCase
{
    private InMemoryObjectCache $objectCache;

    protected function setUp(): void
    {
        $this->objectCache = new InMemoryObjectCache();
    }

    #[TestDox('start() restores the assets of a cached fragment')]
    public function testStartRestoresTheAssetsOfACachedFragment(): void
    {
        $context = ['components' => [['slug' => 'collection', 'dependencies' => []]]];
        $assets = $this->createMock(FragmentAssetContextInterface::class);
        $assets->expects($this->once())->method('beginFragment');
        $assets->expects($this->once())->method('endFragment')->willReturn($context);
        $assets->expects($this->once())->method('restoreFragment')->with($context);

        $this->storeFragment(42, 'module', '<div data-component="collection">Content</div>', $assets);

        ob_start();
        $isMiss = $this->createCache(42, 'module', $assets)->start();
        $output = (string) ob_get_clean();

        static::assertFalse($isMiss);
        static::assertStringContainsString('Content', $output);
    }

    /**
     * @see https://github.com/helsingborg-stad/Municipio/issues/2435
     */
    #[TestDox('stop() stores each fragment of a post in its own cache entry')]
    public function testStopStoresEachFragmentInItsOwnCacheEntry(): void
    {
        $this->storeFragment(42, 'module-a', 'Fragment A');
        $this->storeFragment(42, 'module-b', 'Fragment B');

        static::assertFalse($this->isMiss(42, 'module-a'));
        static::assertFalse($this->isMiss(42, 'module-b'));
        foreach ($this->objectCache->getGroup('modules') as $key => $value) {
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
        $this->objectCache->clearWrites();

        $this->storeFragment(42, 'module-b', 'Fragment B');

        static::assertNotEmpty($this->objectCache->getWrites());
        foreach ($this->objectCache->getWrites() as $write) {
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
        $isMiss = $this->createCache(42, 'module-a')->start();
        $output = (string) ob_get_clean();

        static::assertFalse($isMiss);
        static::assertStringContainsString('Fragment A', $output);
    }

    private function createCache(int $postId, string $module, ?FragmentAssetContextInterface $assets = null): Cache
    {
        return new Cache($postId, $module, 60, null, $assets, new FragmentStore($this->objectCache, 'modules'));
    }

    private function storeFragment(
        int $postId,
        string $module,
        string $markup,
        ?FragmentAssetContextInterface $assets = null,
    ): void {
        ob_start();
        $cache = $this->createCache($postId, $module, $assets);
        $cache->start();
        echo $markup;
        $cache->stop();
        ob_end_clean();
    }

    private function isMiss(int $postId, string $module): bool
    {
        ob_start();
        $cache = $this->createCache($postId, $module);
        $isMiss = $cache->start();
        if ($isMiss) {
            $cache->stop();
        }
        ob_end_clean();

        return $isMiss;
    }
}
