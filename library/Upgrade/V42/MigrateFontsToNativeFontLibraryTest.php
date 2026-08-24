<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests orchestration of the V42 native font migrations.
 */
class MigrateFontsToNativeFontLibraryTest extends TestCase
{
    #[TestDox('migrate() waits for WordPress native font library availability')]
    public function testMigrateWaitsForWordPressNativeFontLibraryAvailability(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $default,
            'postTypeExists' => static fn(string $postType): bool => false,
        ]);

        $legacyGoogleFontsMigrator = $this->createMock(MigrateLegacyGoogleFontsToNativeFontLibrary::class);
        $legacyGoogleFontsMigrator->expects(static::never())->method('migrate');

        (new MigrateFontsToNativeFontLibrary(
            $wpService,
            $legacyGoogleFontsMigrator,
        ))->migrate();

        static::assertArrayNotHasKey('setThemeMod', $wpService->methodCalls);
    }

    #[TestDox('migrate() runs the v42 legacy Google-font migration when the library is available')]
    public function testMigrateRunsTheV42LegacyGoogleFontMigrationWhenTheLibraryIsAvailable(): void
    {
        $wpService = new FakeWpService([
            'postTypeExists' => static fn(string $postType): bool => in_array($postType, ['wp_font_family', 'wp_font_face'], true),
        ]);
        $legacyGoogleFontsMigrator = $this->createMock(MigrateLegacyGoogleFontsToNativeFontLibrary::class);
        $legacyGoogleFontsMigrator->expects(static::once())->method('migrate');

        (new MigrateFontsToNativeFontLibrary(
            $wpService,
            $legacyGoogleFontsMigrator,
        ))->migrate();
    }
}
