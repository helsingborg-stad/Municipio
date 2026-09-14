<?php

namespace ComponentLibrary\Integrations\Image;

interface ImageResolverInterface
{
    public function getImageUrl(int $id, array $size): ?string;

    public function getImageAltText(int $id): ?string;
}

namespace Municipio\Integrations\Component;

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

function get_attached_file(int $id): string|false
{
    $GLOBALS['municipioImageResolverAttachedFileCalls'][] = $id;

    return $GLOBALS['municipioImageResolverAttachedFiles'][$id] ?? false;
}

function apply_filters(string $hookName, mixed $value, mixed ...$args): mixed
{
    $GLOBALS['municipioImageResolverFilterCalls'][] = compact('hookName', 'value', 'args');

    $callback = $GLOBALS['municipioImageResolverFilters'][$hookName] ?? null;

    return is_callable($callback) ? $callback($value, ...$args) : $value;
}

require_once __DIR__ . '/ImageResolver.php';

class ImageResolverTest extends TestCase
{
    private array $temporaryFiles = [];

    protected function setUp(): void
    {
        $GLOBALS['municipioImageResolverImageSrcCalls'] = [];
        $GLOBALS['municipioImageResolverMimeTypeCalls'] = [];
        $GLOBALS['municipioImageResolverAttachedFileCalls'] = [];
        $GLOBALS['municipioImageResolverFilterCalls'] = [];
        $GLOBALS['municipioImageResolverFilters'] = [];
        $GLOBALS['municipioImageResolverMimeTypes'] = [];
        $GLOBALS['municipioImageResolverAttachedFiles'] = [];
        $GLOBALS['municipioImageResolverAltText'] = [];
        $GLOBALS['municipioImageResolverImageSrcCallback'] = static function (int $id, array $size): array {
            $height = $size[1] === false ? 'auto' : (string) $size[1];

            return [sprintf('https://example.com/%d-%sx%s.jpg', $id, $size[0], $height)];
        };
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $temporaryFile) {
            if (is_file($temporaryFile)) {
                unlink($temporaryFile);
            }
        }
    }

    public function testJpegSourcesContinueToReceiveLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][101] = 'image/jpeg';

        $url = (new ImageResolver())->getImageUrl(101, [100, false]);

        $this->assertSame('https://example.com/101-100xauto.jpg', $url);
        $this->assertSame([], $GLOBALS['municipioImageResolverAttachedFileCalls']);
    }

    public function testOpaqueWebpSourcesContinueToReceiveLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][102] = 'image/webp';
        $GLOBALS['municipioImageResolverAttachedFiles'][102] = $this->createTemporaryFile(
            '.webp',
            base64_decode('UklGRiIAAABXRUJQVlA4IBYAAAAwAQCdASoBAAEADsD+JaQAA3AA/vuUAAA=', true),
        );

        $url = (new ImageResolver())->getImageUrl(102, [100, false]);

        $this->assertSame('https://example.com/102-100xauto.jpg', $url);
    }

    public function testOpaqueLosslessWebpSourcesContinueToReceiveLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][110] = 'image/webp';
        $GLOBALS['municipioImageResolverAttachedFiles'][110] = $this->createTemporaryFile(
            '.webp',
            'RIFF' .
            pack('V', 18) .
            'WEBPVP8L' .
            pack('V', 5) .
            "\x2f\x00\x00\x00\x00" .
            "\x00",
        );

        $url = (new ImageResolver())->getImageUrl(110, [100, false]);

        $this->assertSame('https://example.com/110-100xauto.jpg', $url);
    }

    public function testTransparentPngSourcesDoNotReceiveGeneratedLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][103] = 'image/png';
        $GLOBALS['municipioImageResolverAttachedFiles'][103] = $this->createTemporaryFile(
            '.png',
            "\x89PNG\r\n\x1a\n" .
            "\x00\x00\x00\x0dIHDR" .
            "\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00" .
            "\x00\x00\x00\x00",
        );

        $url = (new ImageResolver())->getImageUrl(103, [100, false]);

        $this->assertNull($url);
        $this->assertSame([], $GLOBALS['municipioImageResolverImageSrcCalls']);
    }

    public function testTransparentWebpSourcesDoNotReceiveGeneratedLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][104] = 'image/webp';
        $GLOBALS['municipioImageResolverAttachedFiles'][104] = $this->createTemporaryFile(
            '.webp',
            'RIFF' .
            pack('V', 18) .
            'WEBPVP8X' .
            pack('V', 10) .
            "\x10\x00\x00\x00\x00\x00\x00\x00\x00\x00",
        );

        $url = (new ImageResolver())->getImageUrl(104, [100, false]);

        $this->assertNull($url);
        $this->assertSame([], $GLOBALS['municipioImageResolverImageSrcCalls']);
    }

    public function testTransparentLosslessWebpSourcesDoNotReceiveGeneratedLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][111] = 'image/webp';
        $GLOBALS['municipioImageResolverAttachedFiles'][111] = $this->createTemporaryFile(
            '.webp',
            'RIFF' .
            pack('V', 18) .
            'WEBPVP8L' .
            pack('V', 5) .
            "\x2f\x00\x00\x00\x10" .
            "\x00",
        );

        $url = (new ImageResolver())->getImageUrl(111, [100, false]);

        $this->assertNull($url);
        $this->assertSame([], $GLOBALS['municipioImageResolverImageSrcCalls']);
    }

    public function testTransparentWebpSourcesDoNotRequireTransparencyChunksAtTheStartOfTheFile(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][109] = 'image/webp';
        $GLOBALS['municipioImageResolverAttachedFiles'][109] = $this->createTemporaryFile(
            '.webp',
            'RIFF' .
            pack('V', 326) .
            'WEBP' .
            'JUNK' .
            pack('V', 300) .
            str_repeat("\x00", 300) .
            'VP8X' .
            pack('V', 10) .
            "\x10\x00\x00\x00\x00\x00\x00\x00\x00\x00",
        );

        $url = (new ImageResolver())->getImageUrl(109, [100, false]);

        $this->assertNull($url);
        $this->assertSame([], $GLOBALS['municipioImageResolverImageSrcCalls']);
    }

    public function testTransparentLossyWebpSourcesWithAlphaChunkDoNotReceiveGeneratedLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][115] = 'image/webp';
        $GLOBALS['municipioImageResolverAttachedFiles'][115] = $this->createTemporaryFile(
            '.webp',
            'RIFF' .
            pack('V', 28) .
            'WEBP' .
            'ALPH' .
            pack('V', 2) .
            "\x00\x00" .
            'VP8 ' .
            pack('V', 6) .
            "\x00\x00\x00\x9d\x01\x2a",
        );

        $url = (new ImageResolver())->getImageUrl(115, [100, false]);

        $this->assertNull($url);
        $this->assertSame([], $GLOBALS['municipioImageResolverImageSrcCalls']);
    }

    public function testTransparentGifSourcesDoNotReceiveGeneratedLqipUrls(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][105] = 'image/gif';
        $GLOBALS['municipioImageResolverAttachedFiles'][105] = $this->createTemporaryFile(
            '.gif',
            "GIF89a" .
            "\x01\x00\x01\x00\x00\x00\x00" .
            "\x21\xF9\x04\x01\x00\x00\x00\x00",
        );

        $url = (new ImageResolver())->getImageUrl(105, [100, false]);

        $this->assertNull($url);
        $this->assertSame([], $GLOBALS['municipioImageResolverImageSrcCalls']);
    }

    public function testTransparentImageInspectionIsSkippedForNonLqipSizes(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][106] = 'image/png';
        $GLOBALS['municipioImageResolverAttachedFiles'][106] = $this->createTemporaryFile(
            '.png',
            "\x89PNG\r\n\x1a\n",
        );

        $url = (new ImageResolver())->getImageUrl(106, [800, 600]);

        $this->assertSame('https://example.com/106-800x600.jpg', $url);
        $this->assertSame([], $GLOBALS['municipioImageResolverAttachedFileCalls']);
    }

    public function testSvgBehaviorIsPreserved(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][107] = 'image/svg+xml';

        $url = (new ImageResolver())->getImageUrl(107, [100, false]);

        $this->assertSame('https://example.com/107-100xauto.jpg', $url);
        $this->assertSame([], $GLOBALS['municipioImageResolverAttachedFileCalls']);
    }

    public function testTransparentLqipResultCanBeOverriddenWithFilter(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][108] = 'image/png';
        $GLOBALS['municipioImageResolverAttachedFiles'][108] = $this->createTemporaryFile(
            '.png',
            "\x89PNG\r\n\x1a\n" .
            "\x00\x00\x00\x0dIHDR" .
            "\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00" .
            "\x00\x00\x00\x00",
        );
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

        $url = (new ImageResolver())->getImageUrl(108, [100, false]);

        $this->assertSame('https://example.com/custom-placeholder-108-100-transparent.png', $url);
    }

    public function testLqipFiltersAreAppliedOnEachCallEvenWhenTransparencyDecisionIsCached(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][116] = 'image/png';
        $GLOBALS['municipioImageResolverAttachedFiles'][116] = $this->createTemporaryFile(
            '.png',
            "\x89PNG\r\n\x1a\n" .
            "\x00\x00\x00\x0dIHDR" .
            "\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00" .
            "\x00\x00\x00\x00",
        );
        $GLOBALS['municipioImageResolverFilters']['Municipio/Component/Image/LqipUrl'] = static fn(mixed ...$args): string => 'https://example.com/first.png';

        $resolver = new ImageResolver();

        $firstUrl = $resolver->getImageUrl(116, [100, false]);
        $GLOBALS['municipioImageResolverFilters']['Municipio/Component/Image/LqipUrl'] = static fn(mixed ...$args): string => 'https://example.com/second.png';
        $secondUrl = $resolver->getImageUrl(116, [100, false]);

        $this->assertSame('https://example.com/first.png', $firstUrl);
        $this->assertSame('https://example.com/second.png', $secondUrl);
    }

    public function testLqipTransparencyDecisionIsReusedWithinTheSameRequest(): void
    {
        $GLOBALS['municipioImageResolverMimeTypes'][117] = 'image/webp';
        $GLOBALS['municipioImageResolverAttachedFiles'][117] = $this->createTemporaryFile(
            '.webp',
            'RIFF' .
            pack('V', 18) .
            'WEBPVP8L' .
            pack('V', 5) .
            "\x2f\x00\x00\x00\x10" .
            "\x00",
        );

        $resolver = new ImageResolver();

        $resolver->getImageUrl(117, [100, false]);
        $resolver->getImageUrl(117, [100, false]);

        $this->assertSame([117], $GLOBALS['municipioImageResolverAttachedFileCalls']);
    }

    private function createTemporaryFile(string $suffix, string $contents): string
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'municipio-image-resolver-');
        $this->assertIsString($temporaryFile);

        $renamedTemporaryFile = $temporaryFile . $suffix;
        rename($temporaryFile, $renamedTemporaryFile);
        file_put_contents($renamedTemporaryFile, $contents);

        $this->temporaryFiles[] = $renamedTemporaryFile;

        return $renamedTemporaryFile;
    }
}
