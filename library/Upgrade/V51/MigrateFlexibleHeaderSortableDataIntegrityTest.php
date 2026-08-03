<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V51;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateFlexibleHeaderSortableDataIntegrityTest extends TestCase
{
    #[TestDox('migrate restores empty desktop section order from hidden storage on flexible headers')]
    public function testMigrateRestoresEmptyDesktopSectionOrderFromHiddenStorageOnFlexibleHeaders(): void
    {
        $themeMods = [
            'header_apperance' => 'flexible',
            'header_sortable_section_main_upper' => [],
            'header_sortable_section_main_lower' => [],
            'header_sortable_hidden_storage' => json_encode([
                'header_sortable_section_main_upper' => [
                    'logotype' => ['align' => 'left', 'margin' => 'none'],
                    'search-modal' => ['align' => 'right', 'margin' => 'none'],
                ],
                'header_sortable_section_main_lower' => [
                    'primary' => ['align' => 'right', 'margin' => 'none'],
                ],
            ]),
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateFlexibleHeaderSortableDataIntegrity($wpService))->migrate();

        static::assertContains(
            ['header_sortable_section_main_upper', ['logotype', 'search-modal']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );
        static::assertContains(
            ['header_sortable_section_main_lower', ['primary']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );
    }

    #[TestDox('migrate backfills missing hidden storage options for known items')]
    public function testMigrateBackfillsMissingHiddenStorageOptionsForKnownItems(): void
    {
        $themeMods = [
            'header_apperance' => 'flexible',
            'header_sortable_section_main_upper' => ['logotype', 'user'],
            'header_sortable_section_main_lower' => ['primary'],
            'header_sortable_section_main_upper_responsive' => [],
            'header_sortable_section_main_lower_responsive' => ['primary'],
            'header_sortable_hidden_storage' => json_encode([
                'header_sortable_section_main_upper' => [
                    'logotype' => ['margin' => 'none'],
                ],
            ]),
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateFlexibleHeaderSortableDataIntegrity($wpService))->migrate();

        $storageWrite = $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'header_sortable_hidden_storage');
        $storageWrite = is_string($storageWrite) ? json_decode($storageWrite, true) : $storageWrite;

        static::assertIsArray($storageWrite);
        static::assertSame(
            'left',
            $storageWrite['header_sortable_section_main_upper']['logotype']['align'] ?? null,
        );
        static::assertSame(
            'right',
            $storageWrite['header_sortable_section_main_upper']['user']['align'] ?? null,
        );
        static::assertSame(
            'right',
            $storageWrite['header_sortable_section_main_lower']['primary']['align'] ?? null,
        );
        static::assertSame(
            'none',
            $storageWrite['header_sortable_section_main_lower']['primary']['margin'] ?? null,
        );
    }

    #[TestDox('migrate does nothing for non-flexible header appearance')]
    public function testMigrateDoesNothingForNonFlexibleHeaderAppearance(): void
    {
        $themeMods = [
            'header_apperance' => 'classic',
            'header_sortable_section_main_upper' => [],
            'header_sortable_section_main_lower' => [],
            'header_sortable_hidden_storage' => '{}',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateFlexibleHeaderSortableDataIntegrity($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
    }

    /**
     * Find the last setThemeMod value for a setting key.
     *
     * @param array<int, array<int, mixed>> $setThemeModCalls
     */
    private function findSetThemeModCall(array $setThemeModCalls, string $setting): mixed
    {
        $value = null;

        foreach ($setThemeModCalls as $call) {
            if (($call[0] ?? null) !== $setting) {
                continue;
            }

            $value = $call[1] ?? null;
        }

        return $value;
    }
}
