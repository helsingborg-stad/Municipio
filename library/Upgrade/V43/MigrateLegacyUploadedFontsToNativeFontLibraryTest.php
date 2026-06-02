<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V43;

use Municipio\Upgrade\V43\MigrateLegacyUploadedFontsToNativeFontLibrary as LegacyUploadedFontsMigrator;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
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
            'wpJsonEncode' => static fn(mixed $value): string|false => json_encode($value),
            'wpSlash' => static fn(string|array $value): string|array => is_string($value) ? addslashes($value) : $value,
            'isWpError' => false,
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
        static::assertSame([['upload_dir', '_wp_filter_font_directory']], $wpService->methodCalls['addFilter']);
        static::assertSame([['upload_dir', '_wp_filter_font_directory']], $wpService->methodCalls['removeFilter']);

        if (file_exists($sourceFile)) {
            unlink($sourceFile);
        }
    }

    #[TestDox('mergeUploadedFonts() merges post-bridge uploaded rows with pre-bridge attachment fonts without duplicating the same file')]
    public function testMergeUploadedFontsMergesManagedRowsAndLegacyAttachmentsWithoutDuplicatingTheSameFile(): void
    {
        $wpService = new FakeWpService([
            'sanitizeTitle' => static fn(string $title): string => strtolower(str_replace(' ', '-', $title)),
        ]);
        $migrator = new LegacyUploadedFontsMigrator($wpService);
        $mergeUploadedFonts = new ReflectionMethod(LegacyUploadedFontsMigrator::class, 'mergeUploadedFonts');
        $mergeUploadedFonts->setAccessible(true);

        $fonts = $mergeUploadedFonts->invoke(
            $migrator,
            [[
                'id' => 0,
                'name' => 'Abeezee Merged',
                'type' => 'woff2',
                'url' => 'http://example.com/wp-content/uploads/2023/10/abeezee-merged.woff2',
            ]],
            [[
                'id' => 55,
                'name' => 'ABeeZee',
                'type' => 'woff2',
                'url' => 'http://example.com/wp-content/uploads/2023/10/abeezee-merged.woff2',
            ]],
        );

        static::assertSame(
            [
                [
                    'id' => 55,
                    'name' => 'ABeeZee',
                    'type' => 'woff2',
                    'url' => 'http://example.com/wp-content/uploads/2023/10/abeezee-merged.woff2',
                ],
            ],
            $fonts,
        );
    }

    #[TestDox('getLegacyAttachmentMimeTypes() includes the legacy uploaded font mime types supported by the theme')]
    public function testGetLegacyAttachmentMimeTypesIncludesLegacyUploadedFontMimeTypes(): void
    {
        $migrator = new LegacyUploadedFontsMigrator(new FakeWpService([]));
        $getLegacyAttachmentMimeTypes = new ReflectionMethod(LegacyUploadedFontsMigrator::class, 'getLegacyAttachmentMimeTypes');
        $getLegacyAttachmentMimeTypes->setAccessible(true);

        $mimeTypes = $getLegacyAttachmentMimeTypes->invoke($migrator);

        static::assertContains('font/otf', $mimeTypes);
        static::assertContains('font/ttf', $mimeTypes);
        static::assertContains('font/woff', $mimeTypes);
        static::assertContains('font/woff2', $mimeTypes);
    }
}
