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
use Municipio\Customizer\Fonts\NativeFontLibraryRepository;
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
        ]);
        $nativeFontLibraryRepository = $this->createMock(NativeFontLibraryRepository::class);
        $nativeFontLibraryRepository->expects(static::once())->method('isAvailable')->willReturn(false);

        $kirkiFontsMigrator = $this->createMock(MigrateKirkiFontsToNativeFontLibrary::class);
        $kirkiFontsMigrator->expects(static::never())->method('migrate');

        $uploadedFontsMigrator = $this->createMock(MigrateUploadedFontsToNativeFontLibrary::class);
        $uploadedFontsMigrator->expects(static::never())->method('migrate');

        (new MigrateFontsToNativeFontLibrary(
            $wpService,
            $nativeFontLibraryRepository,
            $kirkiFontsMigrator,
            $uploadedFontsMigrator,
        ))->migrate();

        static::assertArrayNotHasKey('setThemeMod', $wpService->methodCalls);
    }

    #[TestDox('migrate() runs both dedicated native font migrations when the library is available')]
    public function testMigrateRunsBothDedicatedNativeFontMigrationsWhenTheLibraryIsAvailable(): void
    {
        $wpService = new FakeWpService();

        $nativeFontLibraryRepository = $this->createMock(NativeFontLibraryRepository::class);
        $nativeFontLibraryRepository->expects(static::once())->method('isAvailable')->willReturn(true);
        $kirkiFontsMigrator = $this->createMock(MigrateKirkiFontsToNativeFontLibrary::class);
        $kirkiFontsMigrator->expects(static::once())->method('migrate');

        $uploadedFontsMigrator = $this->createMock(MigrateUploadedFontsToNativeFontLibrary::class);
        $uploadedFontsMigrator->expects(static::once())->method('migrate');

        (new MigrateFontsToNativeFontLibrary(
            $wpService,
            $nativeFontLibraryRepository,
            $kirkiFontsMigrator,
            $uploadedFontsMigrator,
        ))->migrate();
    }
}
