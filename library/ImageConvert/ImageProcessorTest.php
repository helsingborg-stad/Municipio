<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Contract\ImageContract;
use Municipio\ImageConvert\Cache\ConversionCache;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use Municipio\ImageConvert\Logging\Log;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

function wp_delete_file(string $path): ?bool
{
    ImageProcessorTest::$deletedPaths[] = $path;
    unlink($path);
    return ImageProcessorTest::$deleteResult;
}

class ImageProcessorTest extends TestCase
{
    public static array $deletedPaths = [];
    public static ?bool $deleteResult = null;

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

    public function testCleansUpTemporaryFilesWithOldAndNewWordPressReturnValues(): void
    {
        foreach ([null, true] as $deleteResult) {
            self::$deletedPaths = [];
            self::$deleteResult = $deleteResult;

            $editor = $this->createMock(\WP_Image_Editor::class);
            $editor->method('get_size')->willReturn(['width' => 100, 'height' => 100]);
            $editor->method('resize')->willReturn(true);
            $editor->method('save')->willReturnCallback(static fn($path) => ['path' => $path]);

            $image = $this->createMock(ImageContract::class);
            $image->method('getPath')->willReturn('/source.jpg');
            $image->method('getWidth')->willReturn(100);
            $image->method('getHeight')->willReturn(100);
            $image->method('getIntermidiateLocation')->willReturn(['path' => 's3://bucket/image.jpg', 'url' => 'https://example.com/image.jpg']);

            $processor = new ImageProcessor(
                new FakeWpService(['wpGetImageEditor' => $editor, 'isWpError' => false]),
                $this->createMock(ImageConvertConfig::class),
                $this->createMock(ConversionCache::class),
                $this->createMock(Log::class),
            );

            static::assertFalse($this->invokePrivateMethod('convertImage', $image, 'jpg', $processor));
            static::assertCount(1, self::$deletedPaths);
            static::assertFileDoesNotExist(self::$deletedPaths[0]);
        }
    }

    private function invokePrivateMethod(string $method, mixed ...$arguments): mixed
    {
        $reflection = new \ReflectionClass(ImageProcessor::class);
        $processor = end($arguments) instanceof ImageProcessor ? array_pop($arguments) : $reflection->newInstanceWithoutConstructor();
        $methodReflection = $reflection->getMethod($method);

        return $methodReflection->invoke($processor, ...$arguments);
    }
}
