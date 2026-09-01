<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V49;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class Version49Test extends TestCase
{
    #[TestDox('upgradeToVersion runs menu fallback consolidation migration')]
    public function testUpgradeToVersionRunsMenuFallbackConsolidationMigration(): void
    {
        $legacyThemeMods = [
            'primary_menu_pagetree_fallback' => true,
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new Version49($wpService))->upgradeToVersion();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);
    }
}
