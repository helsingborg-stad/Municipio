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

namespace Municipio\Upgrade\V42;

use Municipio\Customizer\Fonts\FontCatalog;
use Municipio\Customizer\Fonts\FontRepository;
use Municipio\Customizer\Fonts\NativeFontLibraryRepository;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests migration to the native WordPress font library.
 */
class MigrateFontsToNativeFontLibraryTest extends TestCase
{
    #[TestDox('migrate() does nothing when native font migration has already completed')]
    public function testMigrateDoesNothingWhenNativeFontMigrationHasAlreadyCompleted(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === MigrateFontsToNativeFontLibrary::MIGRATION_SETTING ? true : $default,
        ]);
        $fontRepository = $this->createMock(FontRepository::class);
        $nativeFontLibraryRepository = $this->createMock(NativeFontLibraryRepository::class);
        $nativeFontLibraryRepository->expects(static::never())->method('isAvailable');

        (new MigrateFontsToNativeFontLibrary($wpService, $fontRepository, $nativeFontLibraryRepository))->migrate();

        static::assertArrayNotHasKey('setThemeMod', $wpService->methodCalls);
    }

    #[TestDox('migrate() waits for WordPress native font library availability')]
    public function testMigrateWaitsForWordPressNativeFontLibraryAvailability(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $default,
        ]);
        $fontRepository = $this->createMock(FontRepository::class);
        $nativeFontLibraryRepository = $this->createMock(NativeFontLibraryRepository::class);
        $nativeFontLibraryRepository->expects(static::once())->method('isAvailable')->willReturn(false);

        (new MigrateFontsToNativeFontLibrary($wpService, $fontRepository, $nativeFontLibraryRepository))->migrate();

        static::assertArrayNotHasKey('setThemeMod', $wpService->methodCalls);
    }

    #[TestDox('migrate() creates native families and faces for legacy font settings')]
    public function testMigrateCreatesNativeFamiliesAndFacesForLegacyFontSettings(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static function (string $key, mixed $default): mixed {
                return match ($key) {
                    MigrateFontsToNativeFontLibrary::MIGRATION_SETTING => false,
                    FontCatalog::GOOGLE_FONTS_SETTING => ['Roboto', 'Open Sans', 'Roboto'],
                    'typography_base' => ['font-family' => 'Open Sans', 'variant' => '700'],
                    'typography_heading' => ['font-family' => 'Arial', 'variant' => 'regular'],
                    default => $default,
                };
            },
            'setThemeMod' => true,
        ]);

        $fontRepository = $this->createMock(FontRepository::class);
        $fontRepository
            ->expects(static::once())
            ->method('getUploadedFonts')
            ->willReturn([
                'Inter' => [
                    'id' => 10,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter.woff2',
                ],
            ]);

        $nativeFontLibraryRepository = $this->createMock(NativeFontLibraryRepository::class);
        $nativeFontLibraryRepository->expects(static::once())->method('isAvailable')->willReturn(true);
        $nativeFontLibraryRepository
            ->expects(static::exactly(3))
            ->method('createFontFamilyIfMissing')
            ->willReturnMap([
                ['Roboto', 1],
                ['Open Sans', 2],
                ['Inter', 3],
            ]);
        $nativeFontLibraryRepository
            ->expects(static::once())
            ->method('createFontFaceIfMissing')
            ->with(3, 'Inter', 'https://example.com/inter.woff2');

        (new MigrateFontsToNativeFontLibrary($wpService, $fontRepository, $nativeFontLibraryRepository))->migrate();

        static::assertSame(
            [
                [MigrateFontsToNativeFontLibrary::MIGRATION_SETTING, true],
                [FontCatalog::MIGRATION_SETTING, true],
            ],
            $wpService->methodCalls['setThemeMod'],
        );
    }
}
