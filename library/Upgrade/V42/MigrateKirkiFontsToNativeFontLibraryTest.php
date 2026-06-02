<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

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
                    MigrateKirkiFontsToNativeFontLibrary::LOCAL_INSTALL_SETTING => false,
                    MigrateKirkiFontsToNativeFontLibrary::MIGRATION_SETTING => false,
                    MigrateKirkiFontsToNativeFontLibrary::ACTIVATION_SETTING => false,
                    MigrateKirkiFontsToNativeFontLibrary::LEGACY_GOOGLE_FONTS_SETTING => ['Roboto'],
                    'typography_base' => ['font-family' => 'Roboto', 'variant' => 'regular'],
                    'typography_heading' => ['font-family' => 'Arial', 'variant' => '700'],
                    default => $default,
                };
            },
            'downloadUrl' => static function (string $url): string {
                return match ($url) {
                    'https://fonts.example.com/roboto-400.woff2' => '/tmp/roboto-400.woff2',
                    'https://fonts.example.com/roboto-700.woff2' => '/tmp/roboto-700.woff2',
                    default => '',
                };
            },
            'postTypeExists' => static fn(string $postType): bool => in_array($postType, ['wp_font_family', 'wp_font_face'], true),
            'sanitizeTitle' => static fn(string $title): string => strtolower(str_replace(' ', '-', $title)),
            'wpJsonEncode' => static fn(mixed $value): string|false => json_encode($value),
            'wpSlash' => static fn(string|array $value): string|array => is_string($value) ? addslashes($value) : $value,
            'isWpError' => false,
            'getPageByPath' => null,
            'addFilter' => true,
            'removeFilter' => true,
            'wpHandleSideload' => static function (array $file): array {
                return match ($file['name']) {
                    'roboto-400.woff2' => [
                        'file' => '/var/www/wp-content/uploads/fonts/roboto-400.woff2',
                        'url' => 'https://example.com/wp-content/uploads/fonts/roboto-400.woff2',
                        'type' => 'font/woff2',
                    ],
                    'roboto-700.woff2' => [
                        'file' => '/var/www/wp-content/uploads/fonts/roboto-700.woff2',
                        'url' => 'https://example.com/wp-content/uploads/fonts/roboto-700.woff2',
                        'type' => 'font/woff2',
                    ],
                    default => [],
                };
            },
            'wpGetFontDir' => [
                'basedir' => '/var/www/wp-content/uploads/fonts',
                'baseurl' => 'https://example.com/wp-content/uploads/fonts',
            ],
            'getPosts' => [],
            'wpInsertPost' => static function (array $postarr): int {
                static $postId = 20;

                return ++$postId;
            },
            'addPostMeta' => true,
            'setThemeMod' => true,
        ]);
        $activatedFonts = [];

        (new MigrateKirkiFontsToNativeFontLibrary(
            $wpService,
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
            static function (array $font) use (&$activatedFonts): void {
                $activatedFonts[] = $font;
            },
        ))->migrate();

        static::assertSame(
            [
                ['https://fonts.example.com/roboto-400.woff2'],
                ['https://fonts.example.com/roboto-700.woff2'],
            ],
            $wpService->methodCalls['downloadUrl'],
        );

        static::assertSame(
            [
                [MigrateKirkiFontsToNativeFontLibrary::LOCAL_INSTALL_SETTING, true],
                [MigrateKirkiFontsToNativeFontLibrary::MIGRATION_SETTING,     true],
                [MigrateKirkiFontsToNativeFontLibrary::ACTIVATION_SETTING,    true],
            ],
            $wpService->methodCalls['setThemeMod'],
        );

        static::assertSame(
            ['wp_font_family', 'wp_font_face', 'wp_font_face'],
            array_values(array_map(
                static fn(array $call): string => (string) $call[0]['post_type'],
                $wpService->methodCalls['wpInsertPost'],
            )),
        );
        static::assertCount(2, $wpService->methodCalls['addPostMeta']);

        static::assertCount(1, $activatedFonts);
        static::assertSame(
            ['400', '700'],
            array_values(array_map(
                static fn(array $fontFace): string => (string) $fontFace['fontWeight'],
                $activatedFonts[0]['fontFace'],
            )),
        );
        static::assertSame(
            [
                ['https://example.com/wp-content/uploads/fonts/roboto-400.woff2'],
                ['https://example.com/wp-content/uploads/fonts/roboto-700.woff2'],
            ],
            array_values(array_map(
                static fn(array $fontFace): array => $fontFace['src'],
                $activatedFonts[0]['fontFace'],
            )),
        );
        static::assertSame(
            [
                ['upload_dir', '_wp_filter_font_directory'],
                ['upload_dir', '_wp_filter_font_directory'],
            ],
            $wpService->methodCalls['addFilter'],
        );
        static::assertSame(
            [
                ['upload_dir', '_wp_filter_font_directory'],
                ['upload_dir', '_wp_filter_font_directory'],
            ],
            $wpService->methodCalls['removeFilter'],
        );
    }

    #[TestDox('migrate() activates normal medium semibold bold and matching italic variants for imported Google fonts')]
    public function testMigrateActivatesRequestedGoogleFontVariants(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static function (string $key, mixed $default): mixed {
                return match ($key) {
                    MigrateKirkiFontsToNativeFontLibrary::LOCAL_INSTALL_SETTING => true,
                    MigrateKirkiFontsToNativeFontLibrary::MIGRATION_SETTING => true,
                    MigrateKirkiFontsToNativeFontLibrary::ACTIVATION_SETTING => false,
                    MigrateKirkiFontsToNativeFontLibrary::LEGACY_GOOGLE_FONTS_SETTING => ['Roboto'],
                    'typography_base' => ['font-family' => 'Roboto', 'variant' => 'regular'],
                    default => $default,
                };
            },
            'postTypeExists' => static fn(string $postType): bool => in_array($postType, ['wp_font_family', 'wp_font_face'], true),
            'sanitizeTitle' => static fn(string $title): string => strtolower(str_replace(' ', '-', $title)),
            'setThemeMod' => true,
        ]);

        $activatedFonts = [];

        (new MigrateKirkiFontsToNativeFontLibrary(
            $wpService,
            static fn(): array => [
                'roboto' => [
                    'name' => 'Roboto',
                    'fontFamily' => '"Roboto", sans-serif',
                    'fontFace' => [
                        ['src' => ['https://fonts.example.com/roboto-300.woff2'], 'fontStyle' => 'normal', 'fontWeight' => '300'],
                        ['src' => ['https://fonts.example.com/roboto-400.woff2'], 'fontStyle' => 'normal', 'fontWeight' => '400'],
                        ['src' => ['https://fonts.example.com/roboto-500.woff2'], 'fontStyle' => 'normal', 'fontWeight' => '500'],
                        ['src' => ['https://fonts.example.com/roboto-600.woff2'], 'fontStyle' => 'normal', 'fontWeight' => '600'],
                        ['src' => ['https://fonts.example.com/roboto-700.woff2'], 'fontStyle' => 'normal', 'fontWeight' => '700'],
                        ['src' => ['https://fonts.example.com/roboto-300italic.woff2'], 'fontStyle' => 'italic', 'fontWeight' => '300'],
                        ['src' => ['https://fonts.example.com/roboto-400italic.woff2'], 'fontStyle' => 'italic', 'fontWeight' => '400'],
                        ['src' => ['https://fonts.example.com/roboto-500italic.woff2'], 'fontStyle' => 'italic', 'fontWeight' => '500'],
                        ['src' => ['https://fonts.example.com/roboto-600italic.woff2'], 'fontStyle' => 'italic', 'fontWeight' => '600'],
                        ['src' => ['https://fonts.example.com/roboto-700italic.woff2'], 'fontStyle' => 'italic', 'fontWeight' => '700'],
                    ],
                ],
            ],
            static function (array $font) use (&$activatedFonts): void {
                $activatedFonts[] = $font;
            },
        ))->migrate();

        static::assertCount(1, $activatedFonts);
        static::assertSame(
            ['400', '500', '600', '700', '400italic', '500italic', '600italic', '700italic'],
            array_values(array_map(
                static fn(array $fontFace): string => $fontFace['fontStyle'] === 'italic' ? $fontFace['fontWeight'] . 'italic' : (string) $fontFace['fontWeight'],
                $activatedFonts[0]['fontFace'],
            )),
        );
        static::assertSame(
            [[MigrateKirkiFontsToNativeFontLibrary::ACTIVATION_SETTING, true]],
            $wpService->methodCalls['setThemeMod'],
        );
    }
}
