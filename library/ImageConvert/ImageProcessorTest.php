<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Cache\ConversionCache;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\Logging\Log;
use Municipio\Test\ImageConvert\TestImageStreamWrapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\IsWpError;
use WpService\Contracts\WpAttachmentIs;
use WpService\Contracts\WpDeleteFile;
use WpService\Contracts\WpGetAttachmentMetadata;
use WpService\Contracts\WpGetImageEditor;

class ImageProcessorTest extends TestCase
{
    public function testPrefersTheScaledFileRecordedInAttachmentMetadata(): void
    {
        $image = $this->createMock(ImageContract::class);
        $image->method('getPath')->willReturn('s3://uploads/2026/08/entren-ramlosa-brunnspark-1.jpg');
        $image->method('getUrl')->willReturn('https://media.example/uploads/2026/08/entren-ramlosa-brunnspark-1.jpg');
        $image->expects($this->once())->method('setPath')->with('s3://uploads/2026/08/entren-ramlosa-brunnspark-1-scaled.jpg');
        $image->expects($this->once())->method('setUrl')->with('https://media.example/uploads/2026/08/entren-ramlosa-brunnspark-1-scaled.jpg');

        $this->invokePrivateMethod(
            'preferScaledSource',
            $image,
            [
                'file' => '2026/08/entren-ramlosa-brunnspark-1-scaled.jpg',
                'original_image' => 'entren-ramlosa-brunnspark-1.jpg',
            ],
        );
    }

    public function testUsesTheNumericFileSizeFromAttachmentMetadata(): void
    {
        $size = $this->invokePrivateMethod(
            'getSourceFileSize',
            ['filesize' => 2_107_269],
            '/file/does/not/need/to/be/read.jpg',
        );

        $this->assertSame(2_107_269, $size);
    }

    public function testConvertsExternalStreamThroughLocalValidationBeforePublishingFinalPath(): void
    {
        TestImageStreamWrapper::reset();
        stream_wrapper_register('municipio-test-image', TestImageStreamWrapper::class);

        try {
            $fixturePath = $this->createValidWebpFixture();
            $imageEditor = $this->createMock(\WP_Image_Editor::class);
            $imageEditor->method('get_size')->willReturn([
                'width'  => 1,
                'height' => 1,
            ]);
            $imageEditor->method('resize')->willReturn(true);
            $imageEditor->method('save')->willReturnCallback(static function (string $destinationPath) use ($fixturePath): array {
                copy($fixturePath, $destinationPath);

                return [
                    'path'      => $destinationPath,
                    'file'      => basename($destinationPath),
                    'width'     => 1,
                    'height'    => 1,
                    'mime-type' => 'image/webp',
                    'filesize'  => filesize($destinationPath),
                ];
            });

            /** @var WpGetImageEditor&IsWpError&WpGetAttachmentMetadata&WpAttachmentIs&AddFilter&ApplyFilters&WpDeleteFile&MockObject $wpService */
            $wpService = $this->createMockForIntersectionOfInterfaces([
                WpGetImageEditor::class,
                IsWpError::class,
                WpGetAttachmentMetadata::class,
                WpAttachmentIs::class,
                AddFilter::class,
                ApplyFilters::class,
                WpDeleteFile::class,
            ]);
            $wpService->method('wpGetImageEditor')->willReturn($imageEditor);
            $wpService->method('isWpError')->willReturn(false);
            $wpService->method('applyFilters')->willReturnArgument(1);
            $wpService->expects(static::once())->method('wpDeleteFile')->willReturnCallback(static function (string $path): void {
                if (file_exists($path)) {
                    unlink($path);
                }
            });

            $image = $this->createMock(ImageContract::class);
            $image->method('getPath')->willReturn('/original/image.jpg');
            $image->method('getWidth')->willReturn(425);
            $image->method('getHeight')->willReturn(239);
            $image->method('getIntermidiateLocation')->with('webp')->willReturn([
                'path' => 'municipio-test-image://uploads/Feature_32486-425x239.webp',
                'url'  => 'https://media.example/uploads/Feature_32486-425x239.webp',
            ]);
            $image->expects(static::once())->method('setUrl')->with('https://media.example/uploads/Feature_32486-425x239.webp');
            $image->expects(static::once())->method('setPath')->with('municipio-test-image://uploads/Feature_32486-425x239.webp');

            $conversionCache = $this->createMock(ConversionCache::class);
            $conversionCache->expects(static::once())->method('markConversionSuccess')->with($image);

            $processor = new ImageProcessor(
                $wpService,
                $this->createMock(ImageConvertConfig::class),
                $conversionCache,
                $this->createMock(Log::class),
            );

            $result = $this->invokePrivateMethod(
                'convertImage',
                $image,
                'webp',
                $processor,
            );

            static::assertSame($image, $result);
            static::assertSame(
                ['municipio-test-image://uploads/Feature_32486-425x239.webp'],
                TestImageStreamWrapper::$writtenPaths,
            );
            static::assertSame(
                [],
                array_filter(
                    TestImageStreamWrapper::$writtenPaths,
                    static fn(string $path): bool => str_contains($path, '.tmp-'),
                ),
            );
            unlink($fixturePath);
        } finally {
            stream_wrapper_unregister('municipio-test-image');
        }
    }

    private function invokePrivateMethod(string $method, mixed ...$arguments): mixed
    {
        $reflection = new \ReflectionClass(ImageProcessor::class);
        $processor = end($arguments) instanceof ImageProcessor ? array_pop($arguments) : $reflection->newInstanceWithoutConstructor();
        $methodReflection = $reflection->getMethod($method);

        return $methodReflection->invoke($processor, ...$arguments);
    }

    private function createValidWebpFixture(): string
    {
        $sourcePath = tempnam(sys_get_temp_dir(), 'municipio-webp-fixture-');
        static::assertIsString($sourcePath);
        $sourcePath .= '.webp';

        file_put_contents(
            $sourcePath,
            base64_decode('UklGRiIAAABXRUJQVlA4IBYAAAAwAQCdASoBAAEADsD+JaQAA3AA/vuUAAA=', true),
        );

        return $sourcePath;
    }
}
