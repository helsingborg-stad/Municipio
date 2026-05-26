<?php

namespace Municipio\BackdropBanner;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddAction;
use WpService\Contracts\GetTemplateDirectoryUri;
use WpService\Contracts\RegisterBlockType;
use WpService\Contracts\WpDequeueScript;
use WpService\Contracts\WpDeregisterScript;
use WpService\Contracts\WpEnqueueScript;
use WpService\Contracts\WpEnqueueStyle;
use WpService\Contracts\WpRegisterScript;
use WpService\Contracts\WpRegisterStyle;

class BackdropBannerTest extends TestCase
{
    #[TestDox('addHooks registers the expected WordPress hooks')]
    public function testAddHooksRegistersExpectedWordPressHooks(): void
    {
        $wpService = new class implements AddAction, RegisterBlockType, WpDequeueScript, WpDeregisterScript, GetTemplateDirectoryUri, WpRegisterScript, WpRegisterStyle, WpEnqueueStyle, WpEnqueueScript {
            public array $addActionCalls = [];

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                $this->addActionCalls[] = [$hookName, $callback, $priority, $acceptedArgs];
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                return false;
            }

            public function wpDequeueScript(string $handle): void {}

            public function wpDeregisterScript(string $handle): void {}

            public function getTemplateDirectoryUri(): string
            {
                return 'http://localhost/wp-content/themes/municipio';
            }

            public function wpRegisterScript(string $handle, string|false $src, array $deps = [], string|bool|null $ver = false, array|bool $args = []): bool
            {
                return true;
            }

            public function wpRegisterStyle(string $handle, string|false $src, array $deps = [], string|bool|null $ver = false, string $media = 'all'): bool
            {
                return true;
            }

            public function wpEnqueueStyle(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, string $media = 'all'): void {}

            public function wpEnqueueScript(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, array|bool $args = []): void {}
        };

        $backdropBanner = new BackdropBanner($wpService);
        $backdropBanner->addHooks();

        $this->assertCount(3, $wpService->addActionCalls);
        $this->assertSame('init', $wpService->addActionCalls[0][0]);
        $this->assertSame('customize_controls_enqueue_scripts', $wpService->addActionCalls[1][0]);
        $this->assertSame(1, $wpService->addActionCalls[1][2]);
        $this->assertSame('wp_enqueue_scripts', $wpService->addActionCalls[2][0]);
        $this->assertSame(1, $wpService->addActionCalls[2][2]);
    }

    #[TestDox('excludeFromCustomizer dequeues and deregisters parent and row editor scripts')]
    public function testExcludeFromCustomizerDequeuesAndDeregistersParentAndRowEditorScripts(): void
    {
        $expectedHandles = [
            'municipio-backdrop-banner-block-editor-script',
            'municipio-backdrop-banner-row-editor-script',
        ];

        $wpService = new class implements AddAction, RegisterBlockType, WpDequeueScript, WpDeregisterScript, GetTemplateDirectoryUri, WpRegisterScript, WpRegisterStyle, WpEnqueueStyle, WpEnqueueScript {
            public array $dequeuedScripts = [];
            public array $deregisteredScripts = [];

            public function addAction(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function registerBlockType($blockType, array $args = []): \WP_Block_Type|false
            {
                return false;
            }

            public function wpDequeueScript(string $handle): void
            {
                $this->dequeuedScripts[] = $handle;
            }

            public function wpDeregisterScript(string $handle): void
            {
                $this->deregisteredScripts[] = $handle;
            }

            public function getTemplateDirectoryUri(): string
            {
                return 'http://localhost/wp-content/themes/municipio';
            }

            public function wpRegisterScript(string $handle, string|false $src, array $deps = [], string|bool|null $ver = false, array|bool $args = []): bool
            {
                return true;
            }

            public function wpRegisterStyle(string $handle, string|false $src, array $deps = [], string|bool|null $ver = false, string $media = 'all'): bool
            {
                return true;
            }

            public function wpEnqueueStyle(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, string $media = 'all'): void {}

            public function wpEnqueueScript(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, array|bool $args = []): void {}
        };

        $backdropBanner = new BackdropBanner($wpService);
        $backdropBanner->excludeFromCustomizer();

        foreach ($expectedHandles as $expectedHandle) {
            $this->assertContains($expectedHandle, $wpService->dequeuedScripts, 'Expected script handle was not dequeued.');
            $this->assertContains($expectedHandle, $wpService->deregisteredScripts, 'Expected script handle was not deregistered.');
        }
    }
}
