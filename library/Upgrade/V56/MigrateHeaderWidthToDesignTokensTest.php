<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V56;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateHeaderWidthToDesignTokensTest extends TestCase
{
    #[TestDox('migrate overwrites existing default width token using legacy wide setting')]
    public function testMigrateOverwritesExistingDefaultWidthTokenUsingLegacyWideSetting(): void
    {
        $themeMods = [
            'tokens' => json_encode([
                'token' => [],
                'component' => [
                    '__general__' => [
                        'header' => [
                            '--c-header--container-max-width' => 'var(--container-width-content)',
                        ],
                    ],
                ],
            ]),
            'header_width' => 'wide',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderWidthToDesignTokens($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);

        static::assertSame(
            'var(--container-width-wide)',
            $tokensWrite['component']['__general__']['header']['--c-header--container-max-width'] ?? null,
        );
    }

    #[TestDox('migrate maps legacy widde value to wide width token value')]
    public function testMigrateMapsLegacyWiddeValueToWideWidthTokenValue(): void
    {
        $themeMods = [
            'tokens' => '{}',
            'header_width' => 'widde',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderWidthToDesignTokens($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);

        static::assertSame(
            'var(--container-width-wide)',
            $tokensWrite['component']['__general__']['header']['--c-header--container-max-width'] ?? null,
        );
    }

    #[TestDox('migrate does nothing when legacy width setting is missing')]
    public function testMigrateDoesNothingWhenLegacyWidthSettingIsMissing(): void
    {
        $themeMods = [
            'tokens' => '{}',
            'header_width' => '',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderWidthToDesignTokens($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
    }
}
