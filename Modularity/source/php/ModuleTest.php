<?php

declare(strict_types=1);

namespace Modularity;

use Modularity\Helper\AcfService;
use Modularity\Helper\WpService;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    private \WpService\Implementations\FakeWpService $wpService;

    protected function setUp(): void
    {
        $this->wpService = new \WpService\Implementations\FakeWpService([
            'addAction' => true,
        ]);

        WpService::set($this->wpService);
        AcfService::set(new \AcfService\Implementations\FakeAcfService());
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated()
    {
        $module = new Module();
        static::assertInstanceOf(Module::class, $module);
    }

    #[TestDox('module assets are registered for the frontend outside admin')]
    public function testRegistersFrontendAssetsOutsideAdmin(): void
    {
        $module = new Module();

        $hooks = array_column($this->wpService->methodCalls['addAction'], 0);
        $frontendAssetsCall = array_values(array_filter(
            $this->wpService->methodCalls['addAction'],
            static fn(array $call): bool => $call[0] === 'wp_enqueue_scripts',
        ));

        static::assertContains('wp_enqueue_scripts', $hooks);
        static::assertContains('enqueue_block_assets', $hooks);
        static::assertCount(1, $frontendAssetsCall);
        static::assertSame([$module, 'enqueueFrontendAssets'], $frontendAssetsCall[0][1]);
    }

    #[TestDox('module styles use the shared block editor callback')]
    public function testRegistersSharedBlockEditorStyleCallback(): void
    {
        $module = new Module();
        $blockAssetsCall = array_values(array_filter(
            $this->wpService->methodCalls['addAction'],
            static fn(array $call): bool => $call[0] === 'enqueue_block_assets',
        ));

        static::assertCount(1, $blockAssetsCall);
        static::assertSame([$module, 'enqueueBlockEditorStyle'], $blockAssetsCall[0][1]);
    }

    #[TestDox('modules provide a block editor only style extension point')]
    public function testProvidesBlockEditorStyleExtensionPoint(): void
    {
        $module = new Module();

        static::assertIsCallable([$module, 'blockStyle']);
    }

    #[TestDox('frontend assets are skipped when the module is not present')]
    public function testSkipsFrontendAssetsWhenModuleIsNotPresent(): void
    {
        $module = new ModuleAssetLoadingTestProxy(false);

        $module->enqueueFrontendAssets();

        static::assertSame(0, $module->styleCalls);
        static::assertSame(0, $module->scriptCalls);
    }

    #[TestDox('frontend assets are enqueued when the module is present')]
    public function testEnqueuesFrontendAssetsWhenModuleIsPresent(): void
    {
        $module = new ModuleAssetLoadingTestProxy(true);

        $module->enqueueFrontendAssets();

        static::assertSame(1, $module->styleCalls);
        static::assertSame(1, $module->scriptCalls);
    }

    #[TestDox('block editor assets are not enqueued in the frontend')]
    public function testSkipsBlockEditorAssetsOutsideAdmin(): void
    {
        $module = new ModuleAssetLoadingTestProxy(true);

        $module->enqueueBlockEditorStyle();

        static::assertSame(0, $module->styleCalls);
        static::assertSame(0, $module->blockStyleCalls);
    }

    #[TestDox('hidden modules do not count as present frontend modules')]
    public function testOnlyCollectsVisibleModulePostTypes(): void
    {
        $method = new \ReflectionMethod(Module::class, 'getVisibleModulePostTypes');
        $module = new Module();
        $sidebars = [[
            'modules' => [
                (object) ['post_type' => 'mod-menu', 'hidden' => false],
                (object) ['post_type' => 'mod-table', 'hidden' => true],
            ],
        ]];

        static::assertSame(['mod-menu'], $method->invoke($module, $sidebars));
    }
}

class ModuleAssetLoadingTestProxy extends Module
{
    public int $styleCalls = 0;
    public int $scriptCalls = 0;
    public int $blockStyleCalls = 0;

    public function __construct(private bool $present)
    {
        parent::__construct();
    }

    protected function hasModule(): bool
    {
        return $this->present;
    }

    public function style(): void
    {
        $this->styleCalls++;
    }

    public function script(): void
    {
        $this->scriptCalls++;
    }

    public function blockStyle(): void
    {
        $this->blockStyleCalls++;
    }
}
