<?php

namespace WpService;

class WpService
{
    public function __construct(private array $responses = [])
    {
    }

    public function getPostMimeType(int|\WP_Post $post = null): string|false
    {
        $value = $this->responses['getPostMimeType'] ?? false;

        return is_callable($value) ? $value($post) : $value;
    }

    public function getAttachedFile(int $attachmentId, bool $unfiltered = false): string|false
    {
        $value = $this->responses['getAttachedFile'] ?? false;

        return is_callable($value) ? $value($attachmentId, $unfiltered) : $value;
    }
}

namespace Municipio\ImageConvert;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TransparencyDetector.php';
require_once __DIR__ . '/TransparencyMetadata.php';

class TransparencyMetadataTest extends TestCase
{
    public function testAddsTransparencyMetadataForTransparentImageAttachments(): void
    {
        $detector = $this->createMock(TransparencyDetector::class);
        $detector->expects($this->once())
            ->method('hasTransparency')
            ->with('/tmp/example.png', 'image/png')
            ->willReturn(true);

        $metadata = (new TransparencyMetadata(
            new \WpService\WpService([
                'getPostMimeType' => 'image/png',
                'getAttachedFile' => '/tmp/example.png',
            ]),
            $detector,
        ))->add(['width' => 100], 123);

        $this->assertTrue($metadata[TransparencyMetadata::ATTACHMENT_METADATA_KEY]);
    }

    public function testLeavesMetadataUntouchedForNonImageAttachments(): void
    {
        $detector = $this->createMock(TransparencyDetector::class);
        $detector->expects($this->never())->method('hasTransparency');

        $metadata = ['width' => 100];
        $result = (new TransparencyMetadata(
            new \WpService\WpService([
                'getPostMimeType' => 'application/pdf',
                'getAttachedFile' => '/tmp/example.pdf',
            ]),
            $detector,
        ))->add($metadata, 123);

        $this->assertSame($metadata, $result);
    }

    public function testLeavesMetadataUntouchedWhenFilePathIsUnavailable(): void
    {
        $detector = $this->createMock(TransparencyDetector::class);
        $detector->expects($this->never())->method('hasTransparency');

        $metadata = ['width' => 100];
        $result = (new TransparencyMetadata(
            new \WpService\WpService([
                'getPostMimeType' => 'image/png',
                'getAttachedFile' => false,
            ]),
            $detector,
        ))->add($metadata, 123);

        $this->assertSame($metadata, $result);
    }
}
