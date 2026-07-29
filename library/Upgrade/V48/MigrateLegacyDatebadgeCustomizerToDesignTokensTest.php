<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V48;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateLegacyDatebadgeCustomizerToDesignTokensTest extends TestCase
{
    #[TestDox('migrate maps legacy datebadge setting to datebadge component token and removes deprecated theme mod')]
    public function testMigrateMapsLegacyDatebadgeSettingToDatebadgeComponentTokenAndRemovesDeprecatedThemeMod(): void
    {
        $legacyThemeMods = [
            'tokens' => '{"token":{"--color--primary":"#f00"},"component":{"__general__":{"button":{"--c-button--border-radius":"2px"}}}}',
            'datebadge_color_settings' => 'secondary',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
            'removeThemeMod' => true,
        ]);

        (new MigrateLegacyDatebadgeCustomizerToDesignTokens($wpService))->migrate();

        $setCalls = $wpService->methodCalls['setThemeMod'] ?? [];
        static::assertCount(1, $setCalls);
        static::assertSame('tokens', $setCalls[0][0]);

        $storedTokens = json_decode((string) $setCalls[0][1], true);

        static::assertSame('#f00', $storedTokens['token']['--color--primary']);
        static::assertSame('2px', $storedTokens['component']['__general__']['button']['--c-button--border-radius']);
        static::assertSame('var(--color--secondary)', $storedTokens['component']['__general__']['datebadge']['--c-datebadge--bg']);

        static::assertSame([
            ['datebadge_color_settings'],
        ], $wpService->methodCalls['removeThemeMod'] ?? []);
    }

    #[TestDox('migrate keeps existing datebadge design token value')]
    public function testMigrateKeepsExistingDatebadgeDesignTokenValue(): void
    {
        $legacyThemeMods = [
            'tokens' => '{"component":{"__general__":{"datebadge":{"--c-datebadge--bg":"#123456"}}}}',
            'datebadge_color_settings' => 'primary',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
            'removeThemeMod' => true,
        ]);

        (new MigrateLegacyDatebadgeCustomizerToDesignTokens($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
        static::assertSame([
            ['datebadge_color_settings'],
        ], $wpService->methodCalls['removeThemeMod'] ?? []);
    }

    #[TestDox('migrate removes deprecated theme mod when legacy value is invalid')]
    public function testMigrateRemovesDeprecatedThemeModWhenLegacyValueIsInvalid(): void
    {
        $legacyThemeMods = [
            'tokens' => '',
            'datebadge_color_settings' => 'custom',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $legacyThemeMods[$name] ?? $default,
            'setThemeMod' => true,
            'removeThemeMod' => true,
        ]);

        (new MigrateLegacyDatebadgeCustomizerToDesignTokens($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
        static::assertSame([
            ['datebadge_color_settings'],
        ], $wpService->methodCalls['removeThemeMod'] ?? []);
    }
}
