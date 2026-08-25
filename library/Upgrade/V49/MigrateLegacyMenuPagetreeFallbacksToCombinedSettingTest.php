<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V49;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateLegacyMenuPagetreeFallbacksToCombinedSettingTest extends TestCase
{
    #[TestDox('migrate consolidates legacy menu fallback flags into one combined setting')]
    public function testMigrateConsolidatesLegacyMenuFallbackFlagsIntoOneCombinedSetting(): void
    {
        $legacyThemeMods = [
            'primary_menu_pagetree_fallback' => true,
            'secondary_menu_pagetree_fallback' => '1',
            'mobile_menu_pagetree_fallback' => false,
            'mega_menu_pagetree_fallback' => 1,
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateLegacyMenuPagetreeFallbacksToCombinedSetting($wpService))->migrate();

        static::assertSame(
            [
                ['menu_pagetree_fallback_menus', ['primary', 'secondary', 'mega']],
            ],
            $wpService->methodCalls['setThemeMod'] ?? [],
        );
    }

    #[TestDox('migrate does not overwrite existing combined setting')]
    public function testMigrateDoesNotOverwriteExistingCombinedSetting(): void
    {
        $legacyThemeMods = [
            'menu_pagetree_fallback_menus' => ['mobile'],
            'primary_menu_pagetree_fallback' => true,
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateLegacyMenuPagetreeFallbacksToCombinedSetting($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
    }

    #[TestDox('migrate does not write combined setting when no legacy values exist')]
    public function testMigrateDoesNotWriteCombinedSettingWhenNoLegacyValuesExist(): void
    {
        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $default,
            'setThemeMod' => true,
        ]);

        (new MigrateLegacyMenuPagetreeFallbacksToCombinedSetting($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
    }
}
