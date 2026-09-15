<?php

namespace Municipio\ImageConvert;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TransparencyDetector.php';

class TransparencyDetectorTest extends TestCase
{
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $temporaryFile) {
            if (is_file($temporaryFile)) {
                unlink($temporaryFile);
            }
        }
    }

    public function testDetectsTransparentPng(): void
    {
        $filePath = $this->createTemporaryFile(
            '.png',
            "\x89PNG\r\n\x1a\n" .
            "\x00\x00\x00\x0dIHDR" .
            "\x00\x00\x00\x01\x00\x00\x00\x01\x08\x06\x00\x00\x00" .
            "\x00\x00\x00\x00",
        );

        $this->assertTrue((new TransparencyDetector())->hasTransparency($filePath, 'image/png'));
    }

    public function testDetectsTransparentGif(): void
    {
        $filePath = $this->createTemporaryFile(
            '.gif',
            "GIF89a" .
            "\x01\x00\x01\x00\x00\x00\x00" .
            "\x21\xF9\x04\x01\x00\x00\x00\x00",
        );

        $this->assertTrue((new TransparencyDetector())->hasTransparency($filePath, 'image/gif'));
    }

    public function testDetectsTransparentExtendedWebp(): void
    {
        $filePath = $this->createTemporaryFile(
            '.webp',
            'RIFF' .
            pack('V', 18) .
            'WEBPVP8X' .
            pack('V', 10) .
            "\x10\x00\x00\x00\x00\x00\x00\x00\x00\x00",
        );

        $this->assertTrue((new TransparencyDetector())->hasTransparency($filePath, 'image/webp'));
    }

    public function testDetectsTransparentLosslessWebp(): void
    {
        $filePath = $this->createTemporaryFile(
            '.webp',
            'RIFF' .
            pack('V', 18) .
            'WEBPVP8L' .
            pack('V', 5) .
            "\x2f\x00\x00\x00\x10" .
            "\x00",
        );

        $this->assertTrue((new TransparencyDetector())->hasTransparency($filePath, 'image/webp'));
    }

    public function testDetectsTransparentLossyWebp(): void
    {
        $filePath = $this->createTemporaryFile(
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

        $this->assertTrue((new TransparencyDetector())->hasTransparency($filePath, 'image/webp'));
    }

    public function testDetectsOpaqueLosslessWebp(): void
    {
        $filePath = $this->createTemporaryFile(
            '.webp',
            'RIFF' .
            pack('V', 18) .
            'WEBPVP8L' .
            pack('V', 5) .
            "\x2f\x00\x00\x00\x00" .
            "\x00",
        );

        $this->assertFalse((new TransparencyDetector())->hasTransparency($filePath, 'image/webp'));
    }

    public function testSupportsLaterTransparentWebpChunksWithinScanWindow(): void
    {
        $filePath = $this->createTemporaryFile(
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

        $this->assertTrue((new TransparencyDetector())->hasTransparency($filePath, 'image/webp'));
    }

    public function testReturnsFalseForOpaqueJpeg(): void
    {
        $filePath = $this->createTemporaryFile('.jpg', 'not-a-real-jpeg');

        $this->assertFalse((new TransparencyDetector())->hasTransparency($filePath, 'image/jpeg'));
    }

    private function createTemporaryFile(string $suffix, string $contents): string
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'municipio-transparency-detector-');
        $this->assertIsString($temporaryFile);

        $renamedTemporaryFile = $temporaryFile . $suffix;
        rename($temporaryFile, $renamedTemporaryFile);
        file_put_contents($renamedTemporaryFile, $contents);

        $this->temporaryFiles[] = $renamedTemporaryFile;

        return $renamedTemporaryFile;
    }
}
