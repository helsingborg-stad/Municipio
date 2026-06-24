<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V44;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests v44 migration of the legacy responsive flexible-header order toggle.
 */
class MigrateResponsiveHeaderOrderSettingTest extends TestCase
{
    #[TestDox('migrate() clears responsive sortable values when the legacy responsive order setting was disabled')]
    public function testMigrateClearsResponsiveSortableValuesWhenLegacySettingWasDisabled(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === MigrateResponsiveHeaderOrderSetting::LEGACY_SETTING ? false : $default,
            'setThemeMod' => true,
        ]);

        (new MigrateResponsiveHeaderOrderSetting($wpService))->migrate();

        static::assertSame(
            [
                ['header_sortable_section_main_upper_responsive', []],
                ['header_sortable_section_main_lower_responsive', []],
            ],
            $wpService->methodCalls['setThemeMod'],
        );
        static::assertSame([[MigrateResponsiveHeaderOrderSetting::LEGACY_SETTING]], $wpService->methodCalls['removeThemeMod']);
    }

    #[TestDox('migrate() keeps responsive sortable values when the legacy responsive order setting was enabled')]
    public function testMigrateKeepsResponsiveSortableValuesWhenLegacySettingWasEnabled(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $key, mixed $default): mixed => $key === MigrateResponsiveHeaderOrderSetting::LEGACY_SETTING ? true : $default,
            'setThemeMod' => true,
        ]);

        (new MigrateResponsiveHeaderOrderSetting($wpService))->migrate();

        static::assertArrayNotHasKey('setThemeMod', $wpService->methodCalls);
        static::assertSame([[MigrateResponsiveHeaderOrderSetting::LEGACY_SETTING]], $wpService->methodCalls['removeThemeMod']);
    }
}
