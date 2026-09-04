<?php

namespace Municipio\ImageConvert\Cache;

use Municipio\ImageConvert\Config\ImageConvertConfigInterface;
use Municipio\ImageConvert\Contract\ImageContract;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ConversionCacheTest extends TestCase
{
    public function testSuccessfulStatusRegistersPersistentCacheKey(): void
    {
        $wpService = new FakeWpService([
            'wpCacheGet' => false,
            'wpCacheSet' => true,
        ]);
        $cache = new ConversionCache($wpService, $this->createConfig());

        $this->assertTrue($cache->markConversionSuccess($this->createImage(42, 100, 50)));

        $this->assertSame(
            ['status_v2_42_100x50_webp', 'success', 'municipio_image_convert', 86400],
            $wpService->methodCalls['wpCacheSet'][0],
        );
        $this->assertSame(
            ['keys_42', ['status_v2_42_100x50_webp'], 'municipio_image_convert', 86400],
            $wpService->methodCalls['wpCacheSet'][1],
        );
    }

    public function testClearImageCacheDeletesAllRegisteredPersistentKeys(): void
    {
        $registeredKeys = [
            'status_43_100x50_webp',
            'status_43_200x100_webp',
            'lock_43_200x100_webp',
        ];
        $wpService = new FakeWpService([
            'wpCacheGet' => static fn(string $key): array|false => $key === 'keys_43' ? $registeredKeys : false,
            'wpCacheDelete' => true,
        ]);
        $cache = new ConversionCache($wpService, $this->createConfig());

        $this->assertTrue($cache->clearImageCache(43));

        $this->assertSame(
            [
                ['status_43_100x50_webp', 'municipio_image_convert'],
                ['status_43_200x100_webp', 'municipio_image_convert'],
                ['lock_43_200x100_webp', 'municipio_image_convert'],
                ['keys_43', 'municipio_image_convert'],
            ],
            $wpService->methodCalls['wpCacheDelete'],
        );
    }

    public function testFailedStatusSuppressesOtherSizesOfTheSameImage(): void
    {
        $wpService = new FakeWpService([
            'wpCacheGet' => static fn(string $key): string|false => $key === 'source_failure_44' ? 'failed' : false,
        ]);
        $cache = new ConversionCache($wpService, $this->createConfig());

        $this->assertSame(
            ConversionStatus::Failed,
            $cache->getConversionStatus($this->createImage(44, 200, 100)),
        );
    }

    public function testFailedStatusCreatesSourceLevelRetryMarker(): void
    {
        $wpService = new FakeWpService([
            'wpCacheGet' => false,
            'wpCacheSet' => true,
        ]);
        $cache = new ConversionCache($wpService, $this->createConfig());

        $this->assertTrue($cache->markConversionFailed($this->createImage(45, 100, 50)));

        $this->assertSame(
            ['source_failure_45', 'failed', 'municipio_image_convert', 86400],
            $wpService->methodCalls['wpCacheSet'][2],
        );
    }

    private function createConfig(): ImageConvertConfigInterface
    {
        $config = $this->createStub(ImageConvertConfigInterface::class);
        $config->method('intermidiateImageFormat')->willReturn(['suffix' => 'webp']);
        $config->method('successCacheExpiry')->willReturn(86400);
        $config->method('failedCacheExpiry')->willReturn(86400);
        $config->method('lockExpiry')->willReturn(300);
        $config->method('defaultCacheExpiry')->willReturn(300);
        return $config;
    }

    private function createImage(int $id, int $width, int $height): ImageContract
    {
        $image = $this->createStub(ImageContract::class);
        $image->method('getId')->willReturn($id);
        $image->method('getWidth')->willReturn($width);
        $image->method('getHeight')->willReturn($height);
        return $image;
    }
}
