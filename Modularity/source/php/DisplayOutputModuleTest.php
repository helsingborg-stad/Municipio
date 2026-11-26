<?php

declare(strict_types=1);

namespace Modularity;

use Modularity\Helper\FragmentCache\FragmentStore;
use Municipio\TestUtils\InMemoryObjectCache;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpUtilService\WpUtilServiceInterface;

// Reuses the WordPress function fakes declared for Modularity\Helper\Cache.
require_once __DIR__ . '/Helper/CacheTest.php';

/**
 * Mirrors WordPress: one request-global counter shared by every prefix.
 */
function wp_unique_id(string $prefix = ''): string
{
    return $prefix . (string) ++DisplayOutputModuleTest::$uniqueIdCounter;
}

function get_post_status(mixed $post = null): string
{
    return 'publish';
}

function sanitize_text_field(string $value): string
{
    return $value;
}

class DisplayOutputModuleTest extends TestCase
{
    public static int $uniqueIdCounter = 0;

    private int $renderCount = 0;
    private InMemoryObjectCache $objectCache;

    protected function setUp(): void
    {
        self::$uniqueIdCounter = 0;
        $this->renderCount = 0;
        \Municipio\Styleguide\ComponentAssets\WordPressAssetEnqueuer::setInstance(null);
        ModuleManager::$classes['mod-text'] = get_class(new class {
            public function __construct(mixed ...$args) {}
        });
        $this->objectCache = new InMemoryObjectCache();
    }

    protected function tearDown(): void
    {
        $_GET = [];
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

        // Request 2: something earlier on the page (a menu, a notice, ...) called wp_unique_id().
        self::$uniqueIdCounter = 0;
        wp_unique_id('component-');
        $secondOutput = $this->render($display, $module, $args, $settings);

        static::assertStringContainsString('Module', $firstOutput);
        static::assertStringContainsString('Module', $secondOutput);
        static::assertSame(1, $this->renderCount, 'Expected the second request to be served from the fragment cache.');
        static::assertSame(
            1,
            $this->countStoredFragments(),
            'Expected one fragment for one placement, but a new one was stored per request.',
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

    /**
     * @see https://github.com/helsingborg-stad/Municipio/issues/2435
     */
    #[TestDox('outputModule() does not add fragment cache entries for mod-* query vars the module does not use')]
    public function testOutputModuleDoesNotAddCacheEntriesForUnrelatedModQueryVars(): void
    {
        $module = $this->createModule();
        $display = $this->createDisplay();
        $args = ['id' => 'content-area'];
        $settings = ['cache_ttl' => 60];

        $_GET = [];
        $this->render($display, $module, $args, $settings);

        // Visitors can add any mod-* parameter to the URL.
        $_GET = ['mod-unknown' => 'random-value'];
        $this->render($display, $module, $args, $settings);

        static::assertSame(
            1,
            $this->countStoredFragments(),
            'Expected an unrelated mod-* query var not to store a new fragment.',
        );
    }

    #[TestDox('outputModule() caches a separate fragment for each value of the module\'s own query vars')]
    public function testOutputModuleCachesSeparateFragmentsForTheModulesOwnQueryVars(): void
    {
        $module = $this->createModule();
        $display = $this->createDisplay();
        $args = ['id' => 'content-area'];
        $settings = ['cache_ttl' => 60];

        $_GET = [];
        $this->render($display, $module, $args, $settings);

        $_GET = ['mod-text-123-page' => '2'];
        $this->render($display, $module, $args, $settings);

        static::assertSame(2, $this->renderCount, 'Expected page 2 to be rendered, not served from the page 1 fragment.');
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
        (new \ReflectionProperty(Display::class, 'fragmentStore'))->setValue(
            $display,
            new FragmentStore($this->objectCache, 'modules'),
        );

        return $display;
    }

    private function render(Display $display, \WP_Post $module, array $args, array $settings): string
    {
        ob_start();
        $display->outputModule($module, $args, $settings);

        return (string) ob_get_clean();
    }

    private function countStoredFragments(): int
    {
        return substr_count(serialize($this->objectCache->getGroup('modules')), 'Modularity Fragment Cache');
    }
}
