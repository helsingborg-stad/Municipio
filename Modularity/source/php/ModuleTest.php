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
        new Module();

        $hooks = array_column($this->wpService->methodCalls['addAction'], 0);

        static::assertContains('wp_enqueue_scripts', $hooks);
        static::assertContains('enqueue_block_assets', $hooks);
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
}
