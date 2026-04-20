<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests uploaded font-face rendering.
 */
class UploadedFontFacePrinterTest extends TestCase
{
    #[TestDox('printDeclarations() does not output anything when no uploaded fonts exist')]
    public function testPrintDeclarationsDoesNotOutputAnythingWhenNoUploadedFontsExist(): void
    {
        $wpService = new FakeWpService([
            'escAttr' => static fn(string $value): string => $value,
            'escUrl' => static fn(string $value): string => $value,
            'wpStripAllTags' => static fn(string $value): string => $value,
        ]);

        $fontRepository = $this->createMock(FontRepository::class);
        $fontRepository->method('getUploadedFonts')->willReturn([]);

        $printer = new UploadedFontFacePrinter($wpService, $fontRepository);

        ob_start();
        $printer->printDeclarations();
        $output = (string) ob_get_clean();

        static::assertSame('', $output);
        static::assertStringNotContainsString('fonts.googleapis.com', $output);
        static::assertStringNotContainsString('id="municipio-google-fonts"', $output);
    }

    #[TestDox('printDeclarations() renders uploaded fonts as local @font-face rules')]
    public function testPrintDeclarationsRendersUploadedFontsAsLocalFontFaceRules(): void
    {
        $wpService = new FakeWpService([
            'escAttr' => static fn(string $value): string => $value,
            'escUrl' => static fn(string $value): string => $value,
            'wpStripAllTags' => static fn(string $value): string => $value,
        ]);

        $fontRepository = $this->createMock(FontRepository::class);
        $fontRepository
            ->method('getUploadedFonts')
            ->willReturn([
                'Inter' => [
                    'id' => 10,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter.woff2',
                ],
            ]);

        $printer = new UploadedFontFacePrinter($wpService, $fontRepository);

        ob_start();
        $printer->printDeclarations();
        $output = (string) ob_get_clean();

        static::assertStringContainsString('id="municipio-uploaded-fonts"', $output);
        static::assertStringContainsString('@font-face', $output);
        static::assertStringContainsString('font-family:"Inter"', $output);
        static::assertStringContainsString('https://example.com/inter.woff2', $output);
        static::assertStringNotContainsString('fonts.googleapis.com', $output);
        static::assertStringNotContainsString('id="municipio-google-fonts"', $output);
    }
}
