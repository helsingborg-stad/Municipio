<?php

namespace Municipio\ImageConvert;

use Municipio\ImageConvert\Contract\ImageContract;
use PHPUnit\Framework\TestCase;

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

    private function invokePrivateMethod(string $method, mixed ...$arguments): mixed
    {
        $reflection = new \ReflectionClass(ImageProcessor::class);
        $processor = $reflection->newInstanceWithoutConstructor();
        $methodReflection = $reflection->getMethod($method);

        return $methodReflection->invoke($processor, ...$arguments);
    }
}
