<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests uploaded-font migration to the native WordPress font library.
 */
class MigrateUploadedFontsToNativeFontLibraryTest extends TestCase
{
    #[TestDox('migrate() installs uploaded fonts through the native font upload flow and stores the uploaded-font migration flag')]
    public function testMigrateInstallsUploadedFontsThroughTheNativeFontUploadFlowAndStoresTheMigrationFlag(): void
    {
        $sourceFile = sys_get_temp_dir() . '/inter.woff2';
        file_put_contents($sourceFile, 'font-data');

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === MigrateUploadedFontsToNativeFontLibrary::MIGRATION_SETTING ? false : $default,
            'setThemeMod' => true,
            'postTypeExists' => static fn(string $postType): bool => in_array($postType, ['wp_font_family', 'wp_font_face'], true),
            'sanitizeTitle' => static fn(string $title): string => strtolower(str_replace(' ', '-', $title)),
            'wpJsonEncode' => static fn(mixed $value): string|false => json_encode($value),
            'wpSlash' => static fn(string|array $value): string|array => is_string($value) ? addslashes($value) : $value,
            'isWpError' => false,
            'getPageByPath' => null,
            'wpGetFontDir' => [
                'basedir' => '/var/www/html/wp-content/uploads/fonts',
                'baseurl' => 'http://example.com/wp-content/uploads/fonts',
            ],
            'wpUploadDir' => [
                'basedir' => sys_get_temp_dir(),
                'baseurl' => 'http://example.com/wp-content/uploads',
            ],
            'wpTempnam' => static fn(string $filename = ''): string => tempnam(sys_get_temp_dir(), 'wp-temp-') ?: sys_get_temp_dir() . '/wp-temp-fallback',
            'addFilter' => true,
            'removeFilter' => true,
            'wpHandleSideload' => static function (array $file): array {
                return [
                    'file' => '/var/www/html/wp-content/uploads/fonts/inter.woff2',
                    'url' => 'http://example.com/wp-content/uploads/fonts/inter.woff2',
                    'type' => 'font/woff2',
                ];
            },
            'getPosts' => [],
            'wpInsertPost' => static function (array $postarr): int {
                return $postarr['post_type'] === 'wp_font_family' ? 31 : 32;
            },
            'addPostMeta' => true,
        ]);

        (new MigrateUploadedFontsToNativeFontLibrary(
            $wpService,
            static fn(): array => [[
                'id' => 0,
                'name' => 'Inter',
                'type' => 'woff2',
                'url' => 'http://example.com/wp-content/uploads/inter.woff2',
            ]],
        ))->migrate();

        static::assertSame(
            [[MigrateUploadedFontsToNativeFontLibrary::MIGRATION_SETTING, true]],
            $wpService->methodCalls['setThemeMod'],
        );

        static::assertSame(
            [['name' => 'inter.woff2', 'tmp_name' => $wpService->methodCalls['wpHandleSideload'][0][0]['tmp_name'], 'error' => 0, 'size' => 9]],
            [[
                'name' => $wpService->methodCalls['wpHandleSideload'][0][0]['name'],
                'tmp_name' => $wpService->methodCalls['wpHandleSideload'][0][0]['tmp_name'],
                'error' => $wpService->methodCalls['wpHandleSideload'][0][0]['error'],
                'size' => $wpService->methodCalls['wpHandleSideload'][0][0]['size'],
            ]],
        );

        static::assertSame(
            ['wp_font_family', 'wp_font_face'],
            array_values(array_map(
                static fn(array $call): string => (string) $call[0]['post_type'],
                $wpService->methodCalls['wpInsertPost'],
            )),
        );
        static::assertSame([[32, '_wp_font_face_file', 'inter.woff2']], $wpService->methodCalls['addPostMeta']);
        static::assertSame([['upload_dir', '_wp_filter_font_directory']], $wpService->methodCalls['addFilter']);
        static::assertSame([['upload_dir', '_wp_filter_font_directory']], $wpService->methodCalls['removeFilter']);

        if (file_exists($sourceFile)) {
            unlink($sourceFile);
        }
    }
}
