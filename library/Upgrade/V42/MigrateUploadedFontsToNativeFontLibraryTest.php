<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V42;

use Municipio\Customizer\Fonts\NativeFontLibraryRepository;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests uploaded-font migration to the native WordPress font library.
 */
class MigrateUploadedFontsToNativeFontLibraryTest extends TestCase
{
    #[TestDox('migrate() creates local native font faces for uploaded fonts and stores the uploaded-font migration flag')]
    public function testMigrateCreatesLocalNativeFontFacesForUploadedFontsAndStoresTheMigrationFlag(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === MigrateUploadedFontsToNativeFontLibrary::MIGRATION_SETTING ? false : $default,
            'setThemeMod' => true,
        ]);

        $nativeFontLibraryRepository = $this->createMock(NativeFontLibraryRepository::class);
        $nativeFontLibraryRepository->expects(static::once())->method('isAvailable')->willReturn(true);
        $nativeFontLibraryRepository
            ->expects(static::once())
            ->method('createFontFamilyIfMissing')
            ->with('Inter')
            ->willReturn(31);
        $nativeFontLibraryRepository
            ->expects(static::once())
            ->method('createFontFaceIfMissing')
            ->with(31, 'Inter', 'http://example.com/wp-content/fonts/inter.woff2', 'normal', '100 900', 'inter.woff2');

        (new MigrateUploadedFontsToNativeFontLibrary(
            $wpService,
            $nativeFontLibraryRepository,
            static fn(): array => [[
                'id' => 10,
                'name' => 'Inter',
                'type' => 'woff2',
                'url' => 'http://example.com/wp-content/uploads/inter.woff2',
            ]],
            static fn(array $font): ?array => [
                'source' => 'http://example.com/wp-content/fonts/inter.woff2',
                'fontFile' => 'inter.woff2',
            ],
        ))->migrate();

        static::assertSame(
            [[MigrateUploadedFontsToNativeFontLibrary::MIGRATION_SETTING, true]],
            $wpService->methodCalls['setThemeMod'],
        );
    }
}