<?php

declare(strict_types=1);

namespace Kirki\Module\Webfonts;

if (!class_exists(Fonts::class, false)) {
    /**
     * Test stub for Kirki Google font detection.
     */
    class Fonts
    {
        /**
         * @param string $fontFamily
         *
         * @return bool
         */
        public static function is_google_font(string $fontFamily): bool
        {
            return in_array($fontFamily, ['Roboto', 'Open Sans'], true);
        }
    }
}

namespace Kirki;

if (!class_exists(GoogleFonts::class, false)) {
    /**
     * Test stub for Kirki Google font metadata.
     */
    final class GoogleFonts
    {
        /**
         * @return array<string, array{variants?: array<mixed>}>
         */
        public function get_google_fonts(): array
        {
            return [
                'Roboto' => [
                    'variants' => ['regular', '700italic'],
                ],
                'Open Sans' => [
                    'variants' => ['regular', '700'],
                ],
            ];
        }
    }
}

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

        $kirkiFontsMigrator = $this->createMock(MigrateKirkiFontsToNativeFontLibrary::class);
        $kirkiFontsMigrator->expects(static::never())->method('migrate');

        (new MigrateFontsToNativeFontLibrary(
            $wpService,
            $kirkiFontsMigrator,
        ))->migrate();

        static::assertArrayNotHasKey('setThemeMod', $wpService->methodCalls);
    }

    #[TestDox('migrate() runs the v42 Kirki-font migration when the library is available')]
    public function testMigrateRunsTheV42KirkiFontMigrationWhenTheLibraryIsAvailable(): void
    {
        $wpService = new FakeWpService([
            'postTypeExists' => static fn(string $postType): bool => in_array($postType, ['wp_font_family', 'wp_font_face'], true),
        ]);
        $kirkiFontsMigrator = $this->createMock(MigrateKirkiFontsToNativeFontLibrary::class);
        $kirkiFontsMigrator->expects(static::once())->method('migrate');

        (new MigrateFontsToNativeFontLibrary(
            $wpService,
            $kirkiFontsMigrator,
        ))->migrate();
    }
}
