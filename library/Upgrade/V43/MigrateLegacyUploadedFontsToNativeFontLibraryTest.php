<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V43;

use Municipio\Upgrade\V43\MigrateLegacyUploadedFontsToNativeFontLibrary as LegacyUploadedFontsMigrator;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests v43 migration of legacy uploaded Kirki fonts.
 */
class MigrateLegacyUploadedFontsToNativeFontLibraryTest extends TestCase
{
    #[TestDox('migrate() moves a legacy Kirki attachment font into the native uploaded font manager')]
    public function testMigrateMovesLegacyKirkiAttachmentFontIntoTheNativeUploadedFontManager(): void
    {
        $sourceFile = sys_get_temp_dir() . '/abeezee.woff2';
        file_put_contents($sourceFile, 'font-data');

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => match ($key) {
                LegacyUploadedFontsMigrator::MIGRATION_SETTING => false,
                'municipio_font_catalog_uploaded_fonts' => [['file' => 55]],
                default => $default,
            },
            'setThemeMod' => true,
            'postTypeExists' => static fn(string $postType): bool => in_array($postType, ['wp_font_family', 'wp_font_face'], true),
            'sanitizeTitle' => static fn(string $title): string => strtolower(str_replace(' ', '-', $title)),
            'getPageByPath' => null,
            'wpGetAttachmentUrl' => 'http://example.com/wp-content/uploads/2023/10/abeezee.woff2',
            'wpCheckFiletypeAndExt' => ['ext' => 'woff2'],
            'getAttachedFile' => $sourceFile,
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
            'wpHandleSideload' => static fn(array $file): array => [
                'file' => '/var/www/html/wp-content/uploads/fonts/abeezee.woff2',
                'url' => 'http://example.com/wp-content/uploads/fonts/abeezee.woff2',
                'type' => 'font/woff2',
            ],
            'getPosts' => [],
            'wpInsertPost' => static fn(array $postarr): int => $postarr['post_type'] === 'wp_font_family' ? 71 : 72,
            'addPostMeta' => true,
        ]);

        (new LegacyUploadedFontsMigrator($wpService))->migrate();

        static::assertSame(
            [[LegacyUploadedFontsMigrator::MIGRATION_SETTING, true]],
            $wpService->methodCalls['setThemeMod'],
        );
        static::assertSame(
            ['wp_font_family', 'wp_font_face'],
            array_values(array_map(
                static fn(array $call): string => (string) $call[0]['post_type'],
                $wpService->methodCalls['wpInsertPost'],
            )),
        );
        static::assertSame([[72, '_wp_font_face_file', 'abeezee.woff2']], $wpService->methodCalls['addPostMeta']);
        static::assertSame('abeezee.woff2', $wpService->methodCalls['wpHandleSideload'][0][0]['name']);
        static::assertSame([['upload_dir', '_wp_filter_font_directory', 10, 1]], $wpService->methodCalls['addFilter']);
        static::assertSame([['upload_dir', '_wp_filter_font_directory', 10]], $wpService->methodCalls['removeFilter']);

        if (file_exists($sourceFile)) {
            unlink($sourceFile);
        }
    }
}
