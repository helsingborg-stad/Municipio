<?php

declare(strict_types=1);

namespace Modularity;

use Modularity\Helper\Cache;
use Modularity\Helper\FragmentCache\FragmentStore;
use Municipio\TestUtils\InMemoryObjectCache;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpUtilService\WpUtilServiceInterface;

// Reuses the WordPress function fakes declared for Modularity\Helper\Cache.
require_once __DIR__ . '/Helper/CacheTest.php';

class BlockManagerFragmentCacheTest extends TestCase
{
    private BlockManager $blockManager;
    private object $module;

    protected function setUp(): void
    {
        $this->blockManager = new BlockManager(
            $this->createStub(WpUtilServiceInterface::class),
            new FragmentStore(new InMemoryObjectCache(), 'modules'),
        );

        $this->module = (object) ['ID' => null, 'cacheTtl' => 60, 'data' => []];
        $this->blockManager->classes = ['text' => $this->module];
    }

    protected function tearDown(): void
    {
        $_GET = [];
    }

    /**
     * @see https://github.com/helsingborg-stad/Municipio/issues/2435
     */
    #[TestDox('renderBlock() reuses the fragment cache for a block, even when earlier blocks changed the state of other modules')]
    public function testFragmentCacheIsReusedWhenOtherModulesChangeState(): void
    {
        $block = $this->createBlock(['text' => 'Hello']);
        $this->storeFragment($block);

        // A Posts block rendered earlier on the page left its own data on its module.
        $this->blockManager->classes['posts'] = (object) ['ID' => null, 'data' => ['posts' => ['Post queried in this request']]];

        static::assertFalse($this->isMiss($block), 'Expected the same block to be served from the fragment cache.');
    }

    #[TestDox('renderBlock() caches a separate fragment for blocks with different data')]
    public function testFragmentCacheDiffersForDifferentBlockData(): void
    {
        $this->storeFragment($this->createBlock(['text' => 'Hello']));

        static::assertTrue($this->isMiss($this->createBlock(['text' => 'Goodbye'])));
    }

    /**
     * Blocks render Posts too, and their pagination links are built from the request.
     *
     * @see https://github.com/helsingborg-stad/Municipio/pull/2437#discussion_r4709642752
     */
    #[TestDox('renderBlock() renders without the fragment cache when the URL has a module parameter')]
    public function testFragmentCacheIsNotUsedForRequestsWithModuleParameters(): void
    {
        $block = $this->createBlock(['text' => 'Hello']);
        $this->storeFragment($block);

        $_GET = ['mod-posts-block_abc-page' => '2'];

        static::assertTrue($this->isMiss($block), 'Expected the block to be rendered, not served from the cache.');
    }

    /**
     * A block the way ACF passes it to the render callback: the block type merged with its attributes.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function createBlock(array $data): array
    {
        return [
            'id' => 'block_' . hash('xxh128', serialize($data)),
            'name' => 'acf/text',
            'moduleName' => 'text',
            'mode' => 'edit',
            'render_callback' => [$this->blockManager, 'renderBlock'],
            'data' => $data,
        ];
    }

    /**
     * @param array<string, mixed> $block
     */
    private function storeFragment(array $block): void
    {
        ob_start();
        $cache = $this->createFragmentCache($block);
        $cache->start();
        echo '<p>Block</p>';
        $cache->stop();
        ob_end_clean();
    }

    /**
     * @param array<string, mixed> $block
     */
    private function isMiss(array $block): bool
    {
        ob_start();
        $cache = $this->createFragmentCache($block);
        $isMiss = $cache->start();
        if ($isMiss) {
            $cache->stop();
        }
        ob_end_clean();

        return $isMiss;
    }

    /**
     * @param array<string, mixed> $block
     */
    private function createFragmentCache(array $block): Cache
    {
        $createFragmentCache = new \ReflectionMethod(BlockManager::class, 'createFragmentCache');

        return $createFragmentCache->invoke($this->blockManager, 2, $block, $this->module);
    }
}
