<?php

declare(strict_types=1);

namespace Municipio\Kulturkortet;

use AcfService\AcfService;
use Municipio\Kulturkortet\ProfileEditor\KulturkortetProfileEditorFeature;
use Municipio\Kulturkortet\QRCodeViewer\KulturkortetQRCodeViewerFeature;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;
use WpUtilService\Features\Enqueue\EnqueueManagerInterface;

class KulturkortetAssetsTest extends TestCase
{
    public function testQrAssetsAreEnqueuedOnlyWhenQrBlockIsPresent(): void
    {
        foreach ([false, true] as $blockPresent) {
            $this->assertAssetsForBlock(
                'kulturkortet/qr-code-viewer',
                ['js/kulturkortetQR.js', 'css/kulturkortetQR.css'],
                $blockPresent,
                true,
            );
        }
    }

    public function testProfileEditorCssIsEnqueuedOnlyWhenProfileBlockIsPresent(): void
    {
        foreach ([false, true] as $blockPresent) {
            $this->assertAssetsForBlock(
                'kulturkortet/profile-editor',
                ['css/kulturkortetProfileEditor.css'],
                $blockPresent,
                false,
            );
        }
    }

    private function assertAssetsForBlock(string $blockName, array $assets, bool $blockPresent, bool $qr): void
    {
        $wpService = new FakeWpService(['addAction' => true, 'hasBlock' => function (string $name) use ($blockName, $blockPresent): bool {
            self::assertSame($blockName, $name);
            return $blockPresent;
        }]);
        $enqueued = [];
        $enqueue = $this->createMock(EnqueueManagerInterface::class);
        $enqueue->method('add')->willReturnCallback(function (string $asset) use (&$enqueued, $enqueue) {
            $enqueued[] = $asset;
            return $enqueue;
        });

        $feature = $qr
            ? new KulturkortetQRCodeViewerFeature($wpService, $this->createMock(AcfService::class), $enqueue)
            : new KulturkortetProfileEditorFeature($wpService, $this->createMock(AcfService::class), $enqueue);
        $feature->addHooks();

        self::assertSame([], $enqueued);
        $callbacks = array_values(array_filter(
            $wpService->methodCalls['addAction'],
            static fn(array $call): bool => $call[0] === 'wp_enqueue_scripts',
        ));
        self::assertCount(1, $callbacks);
        $callbacks[0][1]();

        self::assertSame($blockPresent ? $assets : [], $enqueued);
    }
}
