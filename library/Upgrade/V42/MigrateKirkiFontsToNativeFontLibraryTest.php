<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use Municipio\Customizer\Fonts\FontCatalog;
use Municipio\Customizer\Fonts\NativeFontLibraryRepository;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests Kirki-font migration to the native WordPress font library.
 */
class MigrateKirkiFontsToNativeFontLibraryTest extends TestCase
{
    #[TestDox('migrate() installs previously used fonts from WordPress font collections with all available faces')]
    public function testMigrateInstallsPreviouslyUsedFontsFromWordPressFontCollectionsWithAllAvailableFaces(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static function (string $key, mixed $default): mixed {
                return match ($key) {
                    MigrateKirkiFontsToNativeFontLibrary::MIGRATION_SETTING => false,
                    FontCatalog::GOOGLE_FONTS_SETTING => ['Roboto'],
                    'typography_base' => ['font-family' => 'Roboto', 'variant' => 'regular'],
                    'typography_heading' => ['font-family' => 'Arial', 'variant' => '700'],
                    default => $default,
                };
            },
            'setThemeMod' => true,
        ]);

        $nativeFontLibraryRepository = $this->createMock(NativeFontLibraryRepository::class);
        $nativeFontLibraryRepository->expects(static::once())->method('isAvailable')->willReturn(true);
        $nativeFontLibraryRepository->expects(static::once())->method('createFontFamilyIfMissing')->with('Roboto', '"Roboto", sans-serif', 'https://example.com/roboto.svg')->willReturn(21);

        $createdFaces = [];
        $nativeFontLibraryRepository
            ->expects(static::exactly(2))
            ->method('createFontFaceIfMissing')
            ->willReturnCallback(static function (
                int $fontFamilyPostId,
                string $fontFamily,
                string|array $source,
                string $fontStyle,
                string $fontWeight,
                ?string $fontFile = null,
                ?string $unicodeRange = null,
                ?string $preview = null,
            ) use (&$createdFaces): void {
                $createdFaces[] = [
                    'fontFamilyPostId' => $fontFamilyPostId,
                    'fontFamily' => $fontFamily,
                    'source' => $source,
                    'fontStyle' => $fontStyle,
                    'fontWeight' => $fontWeight,
                    'fontFile' => $fontFile,
                    'unicodeRange' => $unicodeRange,
                    'preview' => $preview,
                ];
            });

        (new MigrateKirkiFontsToNativeFontLibrary(
            $wpService,
            $nativeFontLibraryRepository,
            static fn(): array => [
                'roboto' => [
                    'name' => 'Roboto',
                    'fontFamily' => '"Roboto", sans-serif',
                    'preview' => 'https://example.com/roboto.svg',
                    'fontFace' => [
                        [
                            'src' => ['https://fonts.example.com/roboto-400.woff2'],
                            'fontStyle' => 'normal',
                            'fontWeight' => '400',
                            'unicodeRange' => 'U+0000-00FF',
                            'preview' => 'https://example.com/roboto-400-normal.svg',
                        ],
                        [
                            'src' => ['https://fonts.example.com/roboto-700.woff2'],
                            'fontStyle' => 'normal',
                            'fontWeight' => '700',
                            'unicodeRange' => 'U+0000-00FF',
                            'preview' => 'https://example.com/roboto-700-normal.svg',
                        ],
                    ],
                ],
                'open-sans' => [
                    'name' => 'Open Sans',
                    'fontFamily' => '"Open Sans", sans-serif',
                    'fontFace' => [
                        [
                            'src' => ['https://fonts.example.com/open-sans-400.woff2'],
                            'fontStyle' => 'normal',
                            'fontWeight' => '400',
                        ],
                    ],
                ],
            ],
        ))->migrate();

        static::assertSame(
            [
                [
                    'fontFamilyPostId' => 21,
                    'fontFamily' => 'Roboto',
                    'source' => ['https://fonts.example.com/roboto-400.woff2'],
                    'fontStyle' => 'normal',
                    'fontWeight' => '400',
                    'fontFile' => null,
                    'unicodeRange' => 'U+0000-00FF',
                    'preview' => 'https://example.com/roboto-400-normal.svg',
                ],
                [
                    'fontFamilyPostId' => 21,
                    'fontFamily' => 'Roboto',
                    'source' => ['https://fonts.example.com/roboto-700.woff2'],
                    'fontStyle' => 'normal',
                    'fontWeight' => '700',
                    'fontFile' => null,
                    'unicodeRange' => 'U+0000-00FF',
                    'preview' => 'https://example.com/roboto-700-normal.svg',
                ],
            ],
            $createdFaces,
        );

        static::assertSame(
            [[MigrateKirkiFontsToNativeFontLibrary::MIGRATION_SETTING, true]],
            $wpService->methodCalls['setThemeMod'],
        );
    }
}
