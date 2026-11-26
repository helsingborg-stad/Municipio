<?php

declare(strict_types=1);

namespace Modularity;

use Modularity\Helper\FragmentCache\FragmentStore;
use Municipio\TestUtils\InMemoryObjectCache;
use PHPUnit\Framework\Attributes\DataProvider;
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

class DisplayOutputModuleTest extends TestCase
{
    public static int $uniqueIdCounter = 0;

    private int $renderCount = 0;
    private InMemoryObjectCache $objectCache;
    private \WP_Post $module;

    protected function setUp(): void
    {
        self::$uniqueIdCounter = 0;
        $this->renderCount = 0;
        \Municipio\Styleguide\ComponentAssets\WordPressAssetEnqueuer::setInstance(null);
        ModuleManager::$classes['mod-text'] = get_class(new class {
            public function __construct(mixed ...$args) {}
        });
        $this->objectCache = new InMemoryObjectCache();
        $this->module = new \WP_Post((object) []);
        $this->module->ID = 123;
        $this->module->post_type = 'mod-text';
        // WordPress registers its public query vars on the global $wp.
        // @mago-expect lint:no-global
        $GLOBALS['wp'] = (object) ['public_query_vars' => ['s', 'paged', 'p']];
    }

    protected function tearDown(): void
    {
        $_GET = [];
        // @mago-expect lint:no-global
        unset($GLOBALS['wp']);
        unset(ModuleManager::$classes['mod-text']);
    }

    /**
     * @see https://github.com/helsingborg-stad/Municipio/issues/2435
     */
    #[TestDox('outputModule() reuses the fragment cache for the same placement, even when earlier wp_unique_id() calls differ between requests')]
    public function testOutputModuleReusesFragmentCacheAcrossRequestsWithDifferentUniqueIdCounts(): void
    {
        $display = $this->createDisplay();
        $args = ['id' => 'content-area'];
        $settings = ['cache_ttl' => 60];

        // Request 1: nothing else has called wp_unique_id() before the module.
        self::$uniqueIdCounter = 0;
        $firstOutput = $this->render($display, $args, $settings);

        // Request 2: something earlier on the page (a menu, a notice, ...) called wp_unique_id().
        self::$uniqueIdCounter = 0;
        wp_unique_id('component-');
        $secondOutput = $this->render($display, $args, $settings);

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
        $display = $this->createDisplay();
        $args = ['id' => 'content-area'];
        $settings = ['cache_ttl' => 60];

        $firstOutput = $this->render($display, $args, $settings);
        $secondOutput = $this->render($display, $args, $settings);

        static::assertMatchesRegularExpression('/id="mod-text-123-\d+"/', $firstOutput);
        static::assertMatchesRegularExpression('/id="mod-text-123-\d+"/', $secondOutput);
        static::assertNotSame($firstOutput, $secondOutput, 'Expected each placement to get its own wrapper id.');
    }

    /**
     * @see https://github.com/helsingborg-stad/Municipio/issues/2435
     */
    /**
     * @see https://github.com/helsingborg-stad/Municipio/pull/2437#discussion_r4709642643
     * @see https://github.com/helsingborg-stad/Municipio/pull/2437#discussion_r4709642752
     */
    #[TestDox('outputModule() renders without storing a fragment when the URL has $_dataName')]
    #[DataProvider('markupChangingQueryVarProvider')]
    public function testOutputModuleRendersWithoutStoringAFragmentForQueryVarsThatMayChangeMarkup(string $queryVar): void
    {
        $display = $this->createDisplay();
        $args = ['id' => 'content-area'];
        $settings = ['cache_ttl' => 60];

        $_GET = [];
        $this->render($display, $args, $settings);

        // Visitors can add any parameter to the URL, with any value.
        $_GET = [$queryVar => 'random-value'];
        $this->render($display, $args, $settings);

        static::assertSame(2, $this->renderCount, "Expected {$queryVar} to be rendered, not served from the cache.");
        static::assertSame(1, $this->countStoredFragments(), "Expected {$queryVar} not to store a new fragment.");
    }

    /**
     * @return array<string, array{string}>
     */
    public static function markupChangingQueryVarProvider(): array
    {
        return [
            'the module\'s own pagination parameter' => ['mod-text-123-page'],
            'a mod-* parameter the module does not use' => ['mod-text-123-unused'],
            'another module\'s parameter' => ['mod-unknown'],
            'the public query var s' => ['s'],
            'the public query var paged' => ['paged'],
        ];
    }

    #[TestDox('outputModule() serves the cached fragment when the URL only has parameters WordPress does not register')]
    public function testOutputModuleServesTheCachedFragmentForUnregisteredParameters(): void
    {
        $display = $this->createDisplay();
        $args = ['id' => 'content-area'];
        $settings = ['cache_ttl' => 60];

        $_GET = [];
        $this->render($display, $args, $settings);

        $_GET = ['utm_source' => 'newsletter'];
        $this->render($display, $args, $settings);

        static::assertSame(1, $this->renderCount, 'Expected the second request to be served from the fragment cache.');
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

    private function render(Display $display, array $args, array $settings): string
    {
        ob_start();
        $display->outputModule($this->module, $args, $settings);

        return (string) ob_get_clean();
    }

    private function countStoredFragments(): int
    {
        return substr_count(serialize($this->objectCache->getGroup('modules')), 'Modularity Fragment Cache');
    }
}
