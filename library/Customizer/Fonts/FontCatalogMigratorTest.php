<?php

declare(strict_types=1);

namespace Municipio\Customizer\Fonts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests migration of legacy font settings.
 */
class FontCatalogMigratorTest extends TestCase
{
    #[TestDox('migrate() does nothing when migration has already completed')]
    public function testMigrateDoesNothingWhenMigrationHasAlreadyCompleted(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === FontCatalog::MIGRATION_SETTING ? true : $default,
        ]);

        $managedFonts = $this->createMock(ManagedFonts::class);
        $legacyUploadedFontRepository = $this->createMock(LegacyUploadedFontRepository::class);

        $migrator = new FontCatalogMigrator($wpService, $managedFonts, $legacyUploadedFontRepository);
        $migrator->migrate();

        static::assertArrayNotHasKey('setThemeMod', $wpService->methodCalls);
    }

    #[TestDox('migrate() stores merged uploaded fonts and migration flag')]
    public function testMigrateStoresMergedUploadedFontsAndMigrationFlag(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static function (string $key, mixed $default): mixed {
                return match ($key) {
                    FontCatalog::MIGRATION_SETTING => false,
                    FontCatalog::GOOGLE_FONTS_SETTING => ['Roboto'],
                    FontCatalog::UPLOADED_FONTS_SETTING => [['file' => 21]],
                    default => $default,
                };
            },
            'setThemeMod' => true,
        ]);

        $managedFonts = $this->createMock(ManagedFonts::class);
        $managedFonts
            ->expects(static::once())
            ->method('mergeUploadedFontRows')
            ->with(
                [['file' => 21]],
                [['file' => 'https://example.com/inter.woff2']],
            )
            ->willReturn([
                ['file' => 21],
                ['file' => 'https://example.com/inter.woff2'],
            ]);

        $legacyUploadedFontRepository = $this->createMock(LegacyUploadedFontRepository::class);
        $legacyUploadedFontRepository
            ->expects(static::once())
            ->method('getFonts')
            ->willReturn([
                'Inter' => [
                    'id' => 10,
                    'name' => 'Inter',
                    'type' => 'woff2',
                    'url' => 'https://example.com/inter.woff2',
                ],
            ]);

        $migrator = new FontCatalogMigrator($wpService, $managedFonts, $legacyUploadedFontRepository);
        $migrator->migrate();

        static::assertSame(
            [
                [FontCatalog::GOOGLE_FONTS_SETTING, ['Roboto']],
                [FontCatalog::UPLOADED_FONTS_SETTING, [['file' => 21], ['file' => 'https://example.com/inter.woff2']]],
                [FontCatalog::MIGRATION_SETTING, true],
            ],
            $wpService->methodCalls['setThemeMod'],
        );
    }
}
