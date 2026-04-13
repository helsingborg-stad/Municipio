<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class CustomizeTest extends TestCase
{
    #[TestDox('enqueues controls assets in the controls frame only')]
    public function testEnqueueControlsAssetsEnqueuesOnlyControlsScript(): void
    {
        $wpService = new FakeWpService([
            'currentUserCan' => true,
            'getTemplateDirectoryUri' => 'https://example.com/theme',
        ]);

        $customize = new Customize($wpService);
        $customize->enqueueControlsAssets();

        static::assertSame(
            [
                [
                    'municipio-customize',
                    'https://example.com/theme/assets/dist/' . \Municipio\Helper\CacheBust::name('js/customize.js'),
                    ['customize-controls'],
                    false,
                    ['in_footer' => true],
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
            'wpRegisterScript' => true,
        ]);

        $customize = new Customize($wpService);
        $customize->enqueuePreviewAssets();

        static::assertSame(
            [
                [
                    'styleguide-designbuilder',
                    'https://example.com/theme/assets/dist/' . \Municipio\Helper\CacheBust::name('css/designbuilder.css'),
                ],
            ],
            $wpService->methodCalls['wpEnqueueStyle'],
        );

        static::assertSame(
            [
                [
                    'styleguide-designbuilder',
                    'https://example.com/theme/assets/dist/' . \Municipio\Helper\CacheBust::name('js/designbuilder.js'),
                ],
            ],
            $wpService->methodCalls['wpRegisterScript'],
        );

        static::assertSame(
            [
                [
                    'styleguide-designbuilder-preview',
                    'https://example.com/theme/assets/dist/' . \Municipio\Helper\CacheBust::name('js/designbuilder-preview.js'),
                    ['customize-preview', 'styleguide-designbuilder'],
                ],
            ],
            $wpService->methodCalls['wpEnqueueScript'],
        );
    }
}
