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

class DisplayTest extends TestCase
{
    public static int $uniqueIdCounter = 0;

    private int $renderCount = 0;

    protected function setUp(): void
    {
        self::$uniqueIdCounter = 0;
        $this->renderCount = 0;
        \Municipio\Styleguide\ComponentAssets\WordPressAssetEnqueuer::setInstance(null);
        ModuleManager::$classes['mod-text'] = get_class(new class {
            public function __construct(mixed ...$args) {}
        });
        CacheTest::delete(123, 'modules');
    }

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
        $module = $this->createModule();
        $display = $this->createDisplay();
        $args = ['id' => 'content-area'];
        $settings = ['cache_ttl' => 60];

        // Request 1: nothing else has called wp_unique_id() before the module.
        self::$uniqueIdCounter = 0;
        $firstOutput = $this->render($display, $module, $args, $settings);
        $entriesAfterFirstRequest = (array) CacheTest::get(123, 'modules');

        // Request 2: something earlier on the page (a menu, a notice, ...) called wp_unique_id().
        self::$uniqueIdCounter = 0;
        wp_unique_id('component-');
        $secondOutput = $this->render($display, $module, $args, $settings);

        static::assertStringContainsString('Module', $firstOutput);
        static::assertStringContainsString('Module', $secondOutput);
        static::assertSame(1, $this->renderCount, 'Expected the second request to be served from the fragment cache.');
        static::assertNotEmpty($entriesAfterFirstRequest);
        static::assertSame(
            array_keys($entriesAfterFirstRequest),
            array_keys((array) CacheTest::get(123, 'modules')),
            'Expected one fragment for one placement, but a new entry was added per request.',
        );
    }

    #[TestDox('outputModule() gives repeated placements of a cached module distinct wrapper ids')]
    public function testOutputModuleGivesRepeatedCachedPlacementsDistinctWrapperIds(): void
    {
        $module = $this->createModule();
        $display = $this->createDisplay();
        $args = ['id' => 'content-area'];
        $settings = ['cache_ttl' => 60];

        $firstOutput = $this->render($display, $module, $args, $settings);
        $secondOutput = $this->render($display, $module, $args, $settings);

        static::assertMatchesRegularExpression('/id="mod-text-123-\d+"/', $firstOutput);
        static::assertMatchesRegularExpression('/id="mod-text-123-\d+"/', $secondOutput);
        static::assertNotSame($firstOutput, $secondOutput, 'Expected each placement to get its own wrapper id.');
    }

    private function createModule(): \WP_Post
    {
        $module = new \WP_Post((object) []);
        $module->ID = 123;
        $module->post_type = 'mod-text';

        return $module;
    }

    private function createDisplay(): Display
    {
        $display = $this->getMockBuilder(Display::class)->disableOriginalConstructor()->onlyMethods(['getModuleMarkup'])->getMock();
        $display
            ->method('getModuleMarkup')
            ->willReturnCallback(function (mixed $module, array $args): string {
                $this->renderCount++;
                return '<div id="' . $args['modularity_instance_id'] . '">Module</div>';
            });
        (new \ReflectionProperty(Display::class, 'wpUtilService'))->setValue(
            $display,
            $this->createStub(WpUtilServiceInterface::class),
        );

        return $display;
    }

    private function render(Display $display, \WP_Post $module, array $args, array $settings): string
    {
        ob_start();
        $display->outputModule($module, $args, $settings);

        return (string) ob_get_clean();
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
