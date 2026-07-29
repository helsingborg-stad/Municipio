<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V48;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class Version48Test extends TestCase
{
    #[TestDox('upgradeToVersion migrates legacy datebadge setting')]
    public function testUpgradeToVersionMigratesLegacyDatebadgeSetting(): void
    {
        $legacyThemeMods = [
            'tokens' => '',
            'datebadge_color_settings' => 'primary',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
            'removeThemeMod' => true,
        ]);

        (new Version48($wpService))->upgradeToVersion();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);
        static::assertCount(1, $wpService->methodCalls['removeThemeMod'] ?? []);
    }
}
