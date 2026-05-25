<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests managed Google font CSS rendering.
 */
class GoogleFontCssPrinterTest extends TestCase
{
    #[TestDox('printDeclarations() does not output anything when no managed Google fonts should be loaded')]
    public function testPrintDeclarationsDoesNotOutputAnythingWhenNoManagedGoogleFontsShouldBeLoaded(): void
    {
        $wpService = new FakeWpService([
            'applyFilters' => static fn(string $hookName, mixed $value): mixed => $value,
            'escUrl' => static fn(string $value): string => $value,
            'wpStripAllTags' => static fn(string $value): string => $value,
        ]);

        $printer = new GoogleFontCssPrinter(
            $wpService,
            new GoogleFontsUrlBuilder(),
            static fn(): array => [],
            static fn(): array => [],
            static fn(string $url): string => '',
        );

        ob_start();
        $printer->printDeclarations();
        $output = (string) ob_get_clean();

        static::assertSame('', $output);
    }

    #[TestDox('printDeclarations() renders filtered managed Google font CSS inside a style tag')]
    public function testPrintDeclarationsRendersFilteredManagedGoogleFontCssInsideStyleTag(): void
    {
        $wpService = new FakeWpService([
            'applyFilters' => static fn(string $hookName, mixed $value): mixed => is_string($value)
                ? str_replace('latin-ext', 'latin', $value)
                : $value,
            'escUrl' => static fn(string $value): string => $value,
            'wpStripAllTags' => static fn(string $value): string => $value,
        ]);

        $printer = new GoogleFontCssPrinter(
            $wpService,
            new GoogleFontsUrlBuilder(),
            static fn(): array => ['Arimo'],
            static fn(): array => [
                'Arimo' => [
                    'variants' => ['regular', '700italic'],
                ],
            ],
            static fn(string $url): string => sprintf('/* latin-ext */ @font-face{src:url("%s");}', $url),
        );

        ob_start();
        $printer->printDeclarations();
        $output = (string) ob_get_clean();

        static::assertStringContainsString('id="municipio-google-fonts"', $output);
        static::assertStringContainsString('https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400;1,700&display=swap', $output);
        static::assertStringContainsString('/* latin */', $output);
    }

    #[TestDox('printDeclarations() falls back to a stylesheet link when inline Google font CSS is unavailable')]
    public function testPrintDeclarationsFallsBackToStylesheetLinkWhenInlineCssIsUnavailable(): void
    {
        $wpService = new FakeWpService([
            'applyFilters' => static fn(string $hookName, mixed $value): mixed => $value,
            'escUrl' => static fn(string $value): string => $value,
            'wpStripAllTags' => static fn(string $value): string => $value,
        ]);

        $printer = new GoogleFontCssPrinter(
            $wpService,
            new GoogleFontsUrlBuilder(),
            static fn(): array => ['Arimo'],
            static fn(): array => [
                'Arimo' => [
                    'variants' => ['regular'],
                ],
            ],
            static fn(string $url): string => '',
        );

        ob_start();
        $printer->printDeclarations();
        $output = (string) ob_get_clean();

        static::assertStringContainsString('<link id="municipio-google-fonts" rel="stylesheet"', $output);
        static::assertStringContainsString('https://fonts.googleapis.com/css2?family=Arimo:wght@400&display=swap', $output);
        static::assertStringNotContainsString('<style id="municipio-google-fonts">', $output);
    }

    #[TestDox('printDeclarations() builds a fallback stylesheet URL from family names when font metadata is unavailable')]
    public function testPrintDeclarationsBuildsFallbackStylesheetUrlFromFamilyNamesWhenMetadataIsUnavailable(): void
    {
        $wpService = new FakeWpService([
            'applyFilters' => static fn(string $hookName, mixed $value): mixed => $value,
            'escUrl' => static fn(string $value): string => $value,
            'wpStripAllTags' => static fn(string $value): string => $value,
        ]);

        $printer = new GoogleFontCssPrinter(
            $wpService,
            new GoogleFontsUrlBuilder(),
            static fn(): array => ['Arimo'],
            static fn(): array => [],
            static fn(string $url): string => '',
        );

        ob_start();
        $printer->printDeclarations();
        $output = (string) ob_get_clean();

        static::assertStringContainsString('https://fonts.googleapis.com/css2?family=Arimo&display=swap', $output);
        static::assertStringContainsString('<link id="municipio-google-fonts" rel="stylesheet"', $output);
    }
}