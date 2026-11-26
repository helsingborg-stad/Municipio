<?php

declare(strict_types=1);

namespace Modularity;

use Modularity\Helper\CacheTest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpUtilService\WpUtilServiceInterface;

// Reuses the in-memory object cache fakes declared for Modularity\Helper\Cache.
require_once __DIR__ . '/Helper/CacheTest.php';

/**
 * Mirrors WordPress: one request-global counter shared by every prefix.
 */
function wp_unique_id(string $prefix = ''): string
{
    return $prefix . (string) ++DisplayTest::$uniqueIdCounter;
}

function get_post_status(mixed $post = null): string
{
    return 'publish';
}

class DisplayTestFakeModule
{
    public function __construct(mixed ...$args)
    {
    }
}

class DisplayTest extends TestCase
{
    public static int $uniqueIdCounter = 0;

    protected function tearDown(): void
    {
        CacheTest::delete(123, 'modules');
        unset(ModuleManager::$classes['mod-text']);
    }

    /**
     * @see https://github.com/helsingborg-stad/Municipio/issues/2435
     */
    #[TestDox('outputModule() reuses the fragment cache for the same placement, even when earlier wp_unique_id() calls differ between requests')]
    public function testOutputModuleReusesFragmentCacheAcrossRequestsWithDifferentUniqueIdCounts(): void
    {
        \Municipio\Styleguide\ComponentAssets\WordPressAssetEnqueuer::setInstance(null);
        ModuleManager::$classes['mod-text'] = DisplayTestFakeModule::class;
        CacheTest::delete(123, 'modules');

        $module = new \WP_Post((object) []);
        $module->ID = 123;
        $module->post_type = 'mod-text';

        $display = $this->getMockBuilder(Display::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getModuleMarkup'])
            ->getMock();
        $renderCount = 0;
        $display->method('getModuleMarkup')->willReturnCallback(static function () use (&$renderCount): string {
            $renderCount++;
            return '<div>Module</div>';
        });
        (new \ReflectionProperty(Display::class, 'wpUtilService'))->setValue(
            $display,
            $this->createStub(WpUtilServiceInterface::class),
        );

        $args = ['id' => 'content-area'];
        $settings = ['cache_ttl' => 60];

        // Request 1: nothing else has called wp_unique_id() before the module.
        self::$uniqueIdCounter = 0;
        ob_start();
        $display->outputModule($module, $args, $settings);
        $firstOutput = ob_get_clean();

        // Request 2: something earlier on the page (a menu, a notice, ...) called wp_unique_id().
        self::$uniqueIdCounter = 0;
        wp_unique_id('component-');
        ob_start();
        $display->outputModule($module, $args, $settings);
        $secondOutput = ob_get_clean();

        static::assertStringContainsString('Module', $firstOutput);
        static::assertStringContainsString('Module', $secondOutput);
        static::assertSame(1, $renderCount, 'Expected the second request to be served from the fragment cache.');
        static::assertCount(
            2,
            (array) CacheTest::get(123, 'modules'),
            'Expected one fragment (markup + assets) for one placement, but a new entry was added per request.',
        );
    }

    #[TestDox('findModuleDirectory() resolves post type $postType to module directory $expectedDirectory')]
    #[DataProvider('moduleDirectoryProvider')]
    public function testGetModuleDirectoryResolvesPostTypeSlug(string $postType, string $expectedDirectory): void
    {
        $display = (new \ReflectionClass(Display::class))->newInstanceWithoutConstructor();
        $findModuleDirectory = new \ReflectionMethod(Display::class, 'findModuleDirectory');
        $directories = [
            '/modules/NegativeMargin',
            '/modules/Spacer',
        ];

        static::assertSame($expectedDirectory, $findModuleDirectory->invoke($display, $postType, $directories));
    }

    /**
     * Provides post type slugs and their expected module directories.
     *
     * @return array<string, array{string, string}>
     */
    public static function moduleDirectoryProvider(): array
    {
        return [
            'hyphenated slug' => ['mod-negative-margin', 'NegativeMargin'],
            'single-word slug' => ['mod-spacer', 'Spacer'],
        ];
    }
}