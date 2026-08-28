<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V50;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateLegacyHeaderLayoutsToFlexibleTest extends TestCase
{
    #[TestDox('migrate maps casual header to flexible and seeds flexible settings')]
    public function testMigrateMapsCasualHeaderToFlexibleAndSeedsFlexibleSettings(): void
    {
        $themeMods = [
            'header_apperance' => 'casual',
            'casual_header_alignment' => 'casual-center',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateLegacyHeaderLayoutsToFlexible($wpService))->migrate();

        static::assertContains(
            ['header_apperance', 'flexible'],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertContains(
            ['header_sortable_section_main_upper', ['logotype', 'primary']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertContains(
            ['header_sortable_section_main_upper_responsive', ['logotype', 'drawer']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertContains(
            ['header_sortable_section_main_lower_responsive', []],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertNull(
            $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'tokens'),
        );

        $hiddenStorageWrite = $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'header_sortable_hidden_storage');
        $hiddenStorageWrite = is_string($hiddenStorageWrite) ? json_decode($hiddenStorageWrite, true) : $hiddenStorageWrite;
        static::assertIsArray($hiddenStorageWrite);
        static::assertSame('center', $hiddenStorageWrite['header_sortable_section_main_upper']['primary']['align']);
        static::assertSame('left', $hiddenStorageWrite['header_sortable_section_main_upper']['logotype']['align']);
        static::assertArrayHasKey('header_sortable_section_main_lower', $hiddenStorageWrite);
        static::assertArrayHasKey('header_sortable_section_main_upper_responsive', $hiddenStorageWrite);
        static::assertArrayHasKey('header_sortable_section_main_lower_responsive', $hiddenStorageWrite);
    }

    #[TestDox('migrate maps business alignment to flexible lower primary alignment')]
    public function testMigrateMapsBusinessAlignmentToFlexibleLowerPrimaryAlignment(): void
    {
        $themeMods = [
            'header_apperance' => 'business',
            'business_header_alignment' => 'business-left',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateLegacyHeaderLayoutsToFlexible($wpService))->migrate();

        static::assertContains(
            ['header_sortable_section_main_lower', ['primary']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertContains(
            ['header_sortable_section_main_upper_responsive', ['logotype', 'language', 'drawer']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertContains(
            ['header_sortable_section_main_lower_responsive', []],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertContains(
            ['header_sortable_section_main_upper', ['logotype', 'language', 'user']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        $hiddenStorageWrite = $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'header_sortable_hidden_storage');
        $hiddenStorageWrite = is_string($hiddenStorageWrite) ? json_decode($hiddenStorageWrite, true) : $hiddenStorageWrite;
        static::assertSame('left', $hiddenStorageWrite['header_sortable_section_main_lower']['primary']['align']);
        static::assertArrayHasKey('header_sortable_section_main_upper_responsive', $hiddenStorageWrite);
        static::assertArrayHasKey('header_sortable_section_main_lower_responsive', $hiddenStorageWrite);
        static::assertSame('left', $hiddenStorageWrite['header_sortable_section_main_upper_responsive']['logotype']['align']);
        static::assertSame('right', $hiddenStorageWrite['header_sortable_section_main_upper_responsive']['language']['align']);
        static::assertSame('right', $hiddenStorageWrite['header_sortable_section_main_upper_responsive']['drawer']['align']);
        static::assertSame([], $hiddenStorageWrite['header_sortable_section_main_lower_responsive']);

        $tokensWrite = $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'tokens');
        $tokensWrite = is_string($tokensWrite) ? json_decode($tokensWrite, true) : $tokensWrite;
        static::assertIsArray($tokensWrite);
        static::assertSame(
            'var(--color--primary)',
            $tokensWrite['component']['scope:s-header-flexible-lower']['header']['--c-header--color--surface'] ?? null,
        );
        static::assertSame(
            '0',
            $tokensWrite['component']['scope:s-header-flexible-lower']['header']['--c-header--padding-x-enabled'] ?? null,
        );
        static::assertSame(
            '0',
            $tokensWrite['component']['scope:s-header-flexible-lower']['header']['--c-header--padding-y-enabled'] ?? null,
        );
    }

    #[TestDox('migrate removes drawer from mobile header areas when legacy drawer sizes exclude mobile')]
    public function testMigrateRemovesDrawerFromMobileHeaderAreasWhenLegacyDrawerSizesExcludeMobile(): void
    {
        $themeMods = [
            'header_apperance' => 'business',
            'drawer_screen_sizes' => ['lg', 'xl'],
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateLegacyHeaderLayoutsToFlexible($wpService))->migrate();

        static::assertContains(
            ['header_sortable_section_main_upper', ['logotype', 'language', 'drawer', 'user']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );
        static::assertContains(
            ['header_sortable_section_main_upper_responsive', ['logotype', 'language']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );
    }

    #[TestDox('migrate overwrites existing flexible sortable settings when legacy appearance is present')]
    public function testMigrateOverwritesExistingFlexibleSortableSettingsWhenLegacyAppearanceIsPresent(): void
    {
        $themeMods = [
            'header_apperance' => 'casual',
            'header_sortable_section_main_upper' => ['primary'],
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateLegacyHeaderLayoutsToFlexible($wpService))->migrate();

        static::assertContains(
            ['header_apperance', 'flexible'],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertContains(
            ['header_sortable_section_main_upper', ['logotype', 'primary']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertNotNull(
            $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'header_sortable_hidden_storage'),
        );
    }

    #[TestDox('migrate detects markerless casual imports from hidden storage primary signature and applies casual template')]
    public function testMigrateDetectsMarkerlessCasualImportFromHiddenStoragePrimarySignature(): void
    {
        $themeMods = [
            'header_apperance' => 'flexible',
            'header_sortable_hidden_storage' => json_encode([
                'header_sortable_section_main_upper' => [
                    'logotype' => [
                        'align' => 'left',
                        'margin' => 'none',
                    ],
                    'primary' => [
                        'align' => 'right',
                        'margin' => 'none',
                    ],
                ],
            ]),
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateLegacyHeaderLayoutsToFlexible($wpService))->migrate();

        static::assertContains(
            ['header_sortable_section_main_upper', ['logotype', 'primary']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertContains(
            ['header_sortable_section_main_upper_responsive', ['logotype', 'drawer']],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );
    }

    #[TestDox('migrate does not modify already configured flexible header layouts')]
    public function testMigrateDoesNotModifyAlreadyConfiguredFlexibleHeaderLayouts(): void
    {
        $themeMods = [
            'header_apperance' => 'flexible',
            'header_sortable_section_main_upper' => ['logotype', 'language', 'drawer', 'user'],
            'header_sortable_section_main_lower' => ['primary'],
            'header_background' => 'secondary',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateLegacyHeaderLayoutsToFlexible($wpService))->migrate();

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
