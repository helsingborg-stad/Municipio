<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueAssetContext;
use WpUtilService\Features\Enqueue\EnqueueManager;

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
            '_x' => fn($text) => $text,
        ]);
        $enqueue = $this->createMock(EnqueueManager::class);
        $assetContext = $this->createMock(EnqueueAssetContext::class);

        $enqueue->expects(static::exactly(3))
            ->method('add')
            ->willReturnCallback(
                static function (string $src, array $dependencies = []) use ($enqueue): EnqueueManager {
                    static $expectedCalls = [
                        ['css/designbuilder.css', []],
                        ['js/designbuilder.js', []],
                        ['js/designbuilder-preview.js', ['customize-preview', 'js-designbuilderjs']],
                    ];

                    static::assertSame(array_shift($expectedCalls), [$src, $dependencies]);

                    return $enqueue;
                },
            );
        $enqueue->expects(static::once())
            ->method('with')
            ->willReturn($assetContext);
        $assetContext->expects(static::once())
            ->method('translation')
            ->with(
                'styleguide',
                static::callback(static function (array $data): bool {
                    static::assertSame('Show uneditable', $data['translations']['showUneditable']);
                    static::assertSame('Reset all', $data['translations']['resetAll']);

                    return true;
                }),
            )
            ->willReturn($enqueue);

        $customize = new Customize($wpService, $enqueue);
        $customize->enqueuePreviewAssets();
    }

    #[TestDox('does not enqueue design builder assets outside the preview frame')]
    public function testEnqueuePreviewAssetsDoesNothingOutsidePreviewFrame(): void
    {
        $wpService = new FakeWpService(['isCustomizePreview' => false]);
        $enqueue = $this->createMock(EnqueueManager::class);

        $enqueue->expects(static::never())->method('add');

        (new Customize($wpService, $enqueue))->enqueuePreviewAssets();
    }
}
