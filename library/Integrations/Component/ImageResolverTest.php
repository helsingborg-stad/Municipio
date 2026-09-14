<?php

namespace ComponentLibrary\Integrations\Image;

interface ImageResolverInterface
{
    public function getImageUrl(int $id, array $size): ?string;

    public function getImageAltText(int $id): ?string;
}

namespace Municipio\Integrations\Component;

use Municipio\ImageConvert\TransparencyMetadata;
use PHPUnit\Framework\TestCase;

function wp_get_attachment_image_src(int $id, array $size): array|false
{
    $GLOBALS['municipioImageResolverImageSrcCalls'][] = compact('id', 'size');

    $callback = $GLOBALS['municipioImageResolverImageSrcCallback'] ?? null;
    if (is_callable($callback)) {
        return $callback($id, $size);
    }

    return false;
}

function get_post_meta(int $id, string $key, bool $single): string
{
    return $GLOBALS['municipioImageResolverAltText'][$id] ?? '';
}

function get_post_mime_type(int $id): string|false
{
    $GLOBALS['municipioImageResolverMimeTypeCalls'][] = $id;

    return $GLOBALS['municipioImageResolverMimeTypes'][$id] ?? false;
}

function wp_get_attachment_metadata(int $id): array|false
{
    $GLOBALS['municipioImageResolverAttachmentMetadataCalls'][] = $id;

    return $GLOBALS['municipioImageResolverAttachmentMetadata'][$id] ?? false;
}

function apply_filters(string $hookName, mixed $value, mixed ...$args): mixed
{
    $GLOBALS['municipioImageResolverFilterCalls'][] = compact('hookName', 'value', 'args');

    $callback = $GLOBALS['municipioImageResolverFilters'][$hookName] ?? null;

    return is_callable($callback) ? $callback($value, ...$args) : $value;
}

require_once dirname(__DIR__, 2) . '/ImageConvert/TransparencyMetadata.php';
require_once __DIR__ . '/ImageResolver.php';

class ImageResolverTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['municipioImageResolverImageSrcCalls'] = [];
        $GLOBALS['municipioImageResolverMimeTypeCalls'] = [];
        $GLOBALS['municipioImageResolverAttachmentMetadataCalls'] = [];
        $GLOBALS['municipioImageResolverFilterCalls'] = [];
        $GLOBALS['municipioImageResolverFilters'] = [];
        $GLOBALS['municipioImageResolverMimeTypes'] = [];
        $GLOBALS['municipioImageResolverAttachmentMetadata'] = [];
        $GLOBALS['municipioImageResolverAltText'] = [];
        $GLOBALS['municipioImageResolverImageSrcCallback'] = static function (int $id, array $size): array {
            $height = $size[1] === false ? 'auto' : (string) $size[1];

            return [sprintf('https://example.com/%d-%sx%s.jpg', $id, $size[0], $height)];
        };
    }

    public function testJpegSourcesContinueToReceiveLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][101] = 'image/jpeg';
        $GLOBALS['municipioImageResolverAttachmentMetadata'][101] = [
            TransparencyMetadata::ATTACHMENT_METADATA_KEY => false,
        ];

        $url = (new ImageResolver())->getImageUrl(101, [100, false]);

        $this->assertSame('https://example.com/101-100xauto.jpg', $url);
    }

    public function testOpaqueWebpSourcesContinueToReceiveLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][102] = 'image/webp';
        $GLOBALS['municipioImageResolverAttachmentMetadata'][102] = [
            TransparencyMetadata::ATTACHMENT_METADATA_KEY => false,
        ];

        $url = (new ImageResolver())->getImageUrl(102, [100, false]);

        $this->assertSame('https://example.com/102-100xauto.jpg', $url);
    }

    public function testTransparentPngSourcesDoNotReceiveGeneratedLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][103] = 'image/png';
        $GLOBALS['municipioImageResolverAttachmentMetadata'][103] = [
            TransparencyMetadata::ATTACHMENT_METADATA_KEY => true,
        ];

        $url = (new ImageResolver())->getImageUrl(103, [100, false]);

        $this->assertNull($url);
        $this->assertSame([], $GLOBALS['municipioImageResolverImageSrcCalls']);
    }

    public function testTransparentWebpSourcesDoNotReceiveGeneratedLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][104] = 'image/webp';
        $GLOBALS['municipioImageResolverAttachmentMetadata'][104] = [
            TransparencyMetadata::ATTACHMENT_METADATA_KEY => true,
        ];

        $url = (new ImageResolver())->getImageUrl(104, [100, false]);

        $this->assertNull($url);
        $this->assertSame([], $GLOBALS['municipioImageResolverImageSrcCalls']);
    }

    public function testTransparentGifSourcesDoNotReceiveGeneratedLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][105] = 'image/gif';
        $GLOBALS['municipioImageResolverAttachmentMetadata'][105] = [
            TransparencyMetadata::ATTACHMENT_METADATA_KEY => true,
        ];

        $url = (new ImageResolver())->getImageUrl(105, [100, false]);

        $this->assertNull($url);
        $this->assertSame([], $GLOBALS['municipioImageResolverImageSrcCalls']);
    }

    public function testMissingTransparencyMetadataPreservesExistingLqipBehavior(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][106] = 'image/png';

        $url = (new ImageResolver())->getImageUrl(106, [100, false]);

        $this->assertSame('https://example.com/106-100xauto.jpg', $url);
    }

    public function testTransparencyMetadataIsIgnoredForNonLqipSizes(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][107] = 'image/png';
        $GLOBALS['municipioImageResolverAttachmentMetadata'][107] = [
            TransparencyMetadata::ATTACHMENT_METADATA_KEY => true,
        ];

        $url = (new ImageResolver())->getImageUrl(107, [800, 600]);

        $this->assertSame('https://example.com/107-800x600.jpg', $url);
    }

    public function testSvgBehaviorIsPreserved(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][108] = 'image/svg+xml';

        $url = (new ImageResolver())->getImageUrl(108, [100, false]);

        $this->assertSame('https://example.com/108-100xauto.jpg', $url);
    }

    public function testTransparentLqipResultCanBeOverriddenWithFilter(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][109] = 'image/png';
        $GLOBALS['municipioImageResolverAttachmentMetadata'][109] = [
            TransparencyMetadata::ATTACHMENT_METADATA_KEY => true,
        ];
        $GLOBALS['municipioImageResolverFilters']['Municipio/Component/Image/LqipUrl'] = static function (
            mixed $value,
            int $id,
            array $size,
            array $context,
        ): string {
            return sprintf(
                'https://example.com/custom-placeholder-%d-%s-%s.png',
                $id,
                $size[0],
                $context['hasTransparency'] ? 'transparent' : 'opaque',
            );
        };

        $url = (new ImageResolver())->getImageUrl(109, [100, false]);

        $this->assertSame('https://example.com/custom-placeholder-109-100-transparent.png', $url);
    }
}
