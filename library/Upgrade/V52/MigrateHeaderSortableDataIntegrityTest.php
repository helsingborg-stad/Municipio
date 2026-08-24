<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V52;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateHeaderSortableDataIntegrityTest extends TestCase
{
    #[TestDox('migrate repairs legacy business appearance by applying flexible business template')]
    public function testMigrateRepairsLegacyBusinessAppearanceByApplyingFlexibleBusinessTemplate(): void
    {
        $themeMods = [
            'header_apperance' => 'business',
            'header_sortable_section_main_upper' => null,
            'header_sortable_section_main_lower' => null,
            'header_sortable_section_main_upper_responsive' => null,
            'header_sortable_section_main_lower_responsive' => null,
            'header_sortable_hidden_storage' => '{}',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderSortableDataIntegrity($wpService))->migrate();

        static::assertContains(
            ['header_apperance', 'flexible'],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );

        static::assertSame(
            json_encode(['logotype', 'language', 'drawer', 'user']),
            $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'header_sortable_section_main_upper'),
        );
        static::assertSame(
            json_encode(['primary']),
            $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'header_sortable_section_main_lower'),
        );
    }

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

        (new MigrateHeaderSortableDataIntegrity($wpService))->migrate();

        static::assertContains(
            ['header_sortable_section_main_upper', json_encode(['logotype', 'search-modal'])],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );
        static::assertContains(
            ['header_sortable_section_main_lower', json_encode(['primary'])],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );
    }

    #[TestDox('migrate repairs CSV-shaped sortable values into JSON arrays')]
    public function testMigrateRepairsCsvShapedSortableValuesIntoJsonArrays(): void
    {
        $themeMods = [
            'header_apperance' => 'flexible',
            'header_sortable_section_main_upper' => 'logotype,language,drawer,user',
            'header_sortable_section_main_lower' => 'primary',
            'header_sortable_section_main_upper_responsive' => '',
            'header_sortable_section_main_lower_responsive' => '',
            'header_sortable_hidden_storage' => '{}',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderSortableDataIntegrity($wpService))->migrate();

        static::assertSame(
            json_encode(['logotype', 'language', 'drawer', 'user']),
            $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'header_sortable_section_main_upper'),
        );
        static::assertSame(
            json_encode(['primary']),
            $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'header_sortable_section_main_lower'),
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

        (new MigrateHeaderSortableDataIntegrity($wpService))->migrate();

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

        (new MigrateHeaderSortableDataIntegrity($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
    }

    #[TestDox('migrate normalizes CSV sortable values in customizer draft changesets')]
    public function testMigrateNormalizesCsvSortableValuesInCustomizerDraftChangesets(): void
    {
        $changesetData = [
            'municipio::header_sortable_section_main_upper' => [
                'value' => 'logotype,language,drawer,user',
                'type' => 'theme_mod',
            ],
            'municipio::header_sortable_section_main_lower' => [
                'value' => 'primary',
                'type' => 'theme_mod',
            ],
        ];

        $changesetPost = (object) [
            'ID' => 123,
            'post_content' => json_encode($changesetData),
        ];

        $themeMods = [
            'header_apperance' => 'flexible',
            'header_sortable_section_main_upper' => ['logotype', 'language', 'drawer', 'user'],
            'header_sortable_section_main_lower' => ['primary'],
            'header_sortable_section_main_upper_responsive' => [],
            'header_sortable_section_main_lower_responsive' => [],
            'header_sortable_hidden_storage' => '{}',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
            'getPosts' => [$changesetPost],
            'wpUpdatePost' => true,
        ]);

        (new MigrateHeaderSortableDataIntegrity($wpService))->migrate();

        $updateCall = $wpService->methodCalls['wpUpdatePost'][0][0] ?? null;

        static::assertIsArray($updateCall);
        static::assertSame(123, $updateCall['ID'] ?? null);

        $updatedContent = json_decode(stripslashes((string) ($updateCall['post_content'] ?? '')), true);
        static::assertIsArray($updatedContent);
        static::assertSame(
            json_encode(['logotype', 'language', 'drawer', 'user']),
            $updatedContent['municipio::header_sortable_section_main_upper']['value'] ?? null,
        );
        static::assertSame(
            json_encode(['primary']),
            $updatedContent['municipio::header_sortable_section_main_lower']['value'] ?? null,
        );
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
