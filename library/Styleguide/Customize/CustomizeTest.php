<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManager;
use WpUtilService\WpUtilService;

class CustomizeTest extends TestCase
{
    #[TestDox('enqueues controls assets in the controls frame only')]
    public function testEnqueueControlsAssetsEnqueuesOnlyControlsScript(): void
    {
        $wpService = new FakeWpService([
            'currentUserCan' => true,
            'getTemplateDirectoryUri' => 'https://example.com/theme',
        ]);

        $customize = new Customize($wpService, new EnqueueManager($wpService));
        $customize->enqueueControlsAssets();

        static::assertSame(
            [
                [
                    'municipio-customize',
                    'https://example.com/theme/assets/dist/' . \Municipio\Helper\CacheBust::name('js/customize.js'),
                    ['customize-controls'],
                ],
            ],
            $wpService->methodCalls['wpEnqueueScript'],
        );
        static::assertArrayNotHasKey('wpEnqueueStyle', $wpService->methodCalls);
    }

    #[TestDox('enqueues design builder assets in the preview frame only')]
    public function testEnqueuePreviewAssetsEnqueuesPreviewRuntimeAssets(): void
    {
        $wpService = new FakeWpService([
            'isCustomizePreview' => true,
            'currentUserCan' => true,
            'getTemplateDirectoryUri' => 'https://example.com/theme',
            '_x' => fn($text) => $text,
            'getSiteUrl' => 'https://example.com',
            'addFilter' => true,
            'wpCacheGet' => false,
            'wpCacheSet' => true,
            'wpRegisterStyle' => true,
            'wpEnqueueStyle' => true,
            'wpRegisterScript' => true,
            'wpEnqueueScript' => true,
            'wpLocalizeScript' => true,
        ]);

        $customize = new Customize(
            $wpService,
            (new WpUtilService($wpService))->enqueue(dirname(__DIR__, 3)),
        );
        $customize->enqueuePreviewAssets();

        static::assertSame(
            [
                [
                    'css-designbuildercss',
                ],
            ],
            $wpService->methodCalls['wpEnqueueStyle'],
        );

        static::assertSame(
            [
                [
                    'js-designbuilderjs',
                ],
                [
                    'js-designbuilder-previewjs',
                ],
            ],
            $wpService->methodCalls['wpEnqueueScript'],
        );

        $registeredScripts = $wpService->methodCalls['wpRegisterScript'];
        static::assertSame('js-designbuilderjs', $registeredScripts[0][0]);
        static::assertMatchesRegularExpression('#/assets/dist/js/designbuilder\.[\w-]+\.js$#', $registeredScripts[0][1]);
        static::assertSame([], $registeredScripts[0][2]);
        static::assertSame('js-designbuilder-previewjs', $registeredScripts[1][0]);
        static::assertMatchesRegularExpression('#/assets/dist/js/designbuilder-preview\.[\w-]+\.js$#', $registeredScripts[1][1]);
        static::assertSame(['customize-preview', 'js-designbuilderjs'], $registeredScripts[1][2]);

        static::assertCount(2, $wpService->methodCalls['addFilter']);
    }
}
