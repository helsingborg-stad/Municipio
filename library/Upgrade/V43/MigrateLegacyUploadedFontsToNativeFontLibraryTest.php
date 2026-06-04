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
            'wpDeletePost' => null,
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
        static::assertContains(
            [LegacyUploadedFontsMigrator::MIGRATION_SETTING, true],
            $wpService->methodCalls['setThemeMod'],
        );
        static::assertNull($wpService->methodCalls['wpDeletePost']);

        if (file_exists($sourceFile)) {
            unlink($sourceFile);
        }
    }

    #[TestDox('migrate() activates all migrated uploaded font faces for a family by default')]
    public function testMigrateActivatesAllMigratedUploadedFontFacesByDefault(): void
    {
        $storedFontFaces = [];
        $activatedFonts = [];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => match ($key) {
                LegacyUploadedFontsMigrator::MIGRATION_SETTING => false,
                'municipio_native_font_library_legacy_uploaded_fonts_v43_activated' => false,
                default => $default,
            },
            'setThemeMod' => true,
            'postTypeExists' => static fn(string $postType): bool => in_array($postType, ['wp_font_family', 'wp_font_face'], true),
            'sanitizeTitle' => static fn(string $title): string => strtolower(str_replace(' ', '-', $title)),
            'wpJsonEncode' => static fn(mixed $value): string|false => json_encode($value),
            'wpSlash' => static fn(string|array $value): string|array => is_string($value) ? addslashes($value) : $value,
            'isWpError' => false,
            'getPageByPath' => null,
            'getPosts' => static function (array $query) use (&$storedFontFaces): array {
                if (($query['post_type'] ?? null) !== 'wp_font_face' || ($query['post_parent'] ?? 0) !== 71) {
                    return [];
                }

                return $storedFontFaces;
            },
            'wpInsertPost' => static function (array $postarr) use (&$storedFontFaces): int {
                if ($postarr['post_type'] === 'wp_font_family') {
                    return 71;
                }

                $faceId = 72 + count($storedFontFaces);
                $storedFontFaces[] = (object) [
                    'ID' => $faceId,
                    'post_content' => stripslashes((string) $postarr['post_content']),
                ];

                return $faceId;
            },
            'addPostMeta' => true,
        ]);

        (new LegacyUploadedFontsMigrator(
            $wpService,
            static fn(): array => [
                [
                    'id' => 0,
                    'name' => 'ABeeZee',
                    'type' => 'woff2',
                    'url' => 'https://fonts.example.com/abeezee-400.woff2',
                ],
                [
                    'id' => 0,
                    'name' => 'ABeeZee',
                    'type' => 'woff2',
                    'url' => 'https://fonts.example.com/abeezee-700.woff2',
                ],
            ],
            static fn(array $font): ?array => [
                'source' => $font['url'],
                'fontFile' => null,
            ],
            static function (array $activatedFont) use (&$activatedFonts): bool {
                $activatedFonts[] = $activatedFont;

                return true;
            },
        ))->migrate();

        static::assertCount(1, $activatedFonts);
        static::assertSame('ABeeZee', $activatedFonts[0]['name']);
        static::assertSame('abeezee', $activatedFonts[0]['slug']);
        static::assertSame(
            [
                ['https://fonts.example.com/abeezee-400.woff2'],
                ['https://fonts.example.com/abeezee-700.woff2'],
            ],
            array_values(array_map(
                static fn(array $fontFace): array => $fontFace['src'],
                $activatedFonts[0]['fontFace'],
            )),
        );
        static::assertContains(
            ['municipio_native_font_library_legacy_uploaded_fonts_v43_activated', true],
            $wpService->methodCalls['setThemeMod'],
        );
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

    #[TestDox('migrate() ignores attachment slug collisions and still creates native font families for uploaded fonts')]
    public function testMigrateIgnoresAttachmentSlugCollisionsWhenCreatingNativeFontFamilies(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === LegacyUploadedFontsMigrator::MIGRATION_SETTING ? false : $default,
            'setThemeMod' => true,
            'postTypeExists' => static fn(string $postType): bool => in_array($postType, ['wp_font_family', 'wp_font_face'], true),
            'sanitizeTitle' => static fn(string $title): string => strtolower(str_replace(' ', '-', $title)),
            'wpJsonEncode' => static fn(mixed $value): string|false => json_encode($value),
            'wpSlash' => static fn(string|array $value): string|array => is_string($value) ? addslashes($value) : $value,
            'isWpError' => false,
            'getPageByPath' => [
                'ID' => 4076,
                'post_type' => 'attachment',
            ],
            'getPosts' => [],
            'wpInsertPost' => static fn(array $postarr): int => $postarr['post_type'] === 'wp_font_family' ? 91 : 92,
            'addPostMeta' => true,
        ]);

        (new LegacyUploadedFontsMigrator(
            $wpService,
            static fn(): array => [[
                'id' => 0,
                'name' => 'Helsingborg Sans Bold',
                'type' => 'woff',
                'url' => 'https://media.helsingborg.se/uploads/networks/4/sites/131/2023/01/helsingborg-sans-bold.woff',
            ]],
            static fn(array $font): ?array => [
                'source' => $font['url'],
                'fontFile' => null,
            ],
        ))->migrate();

        static::assertSame(
            ['wp_font_family', 'wp_font_face'],
            array_values(array_map(
                static fn(array $call): string => (string) $call[0]['post_type'],
                $wpService->methodCalls['wpInsertPost'],
            )),
        );
        static::assertSame(91, $wpService->methodCalls['wpInsertPost'][1][0]['post_parent']);
        static::assertContains(
            [LegacyUploadedFontsMigrator::MIGRATION_SETTING, true],
            $wpService->methodCalls['setThemeMod'],
        );
    }
}
