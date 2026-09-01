<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V54;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateHeaderAppearanceToDesignTokensTest extends TestCase
{
    #[TestDox('migrate writes legacy header appearance values to matching header design token keys')]
    public function testMigrateWritesLegacyHeaderAppearanceValuesToMatchingHeaderDesignTokenKeys(): void
    {
        $themeMods = [
            'tokens' => '{}',
            'header_background_upper' => 'secondary',
            'header_background' => 'primary',
            'header_width' => 'wide',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderAppearanceToDesignTokens($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);
        static::assertSame('tokens', $wpService->methodCalls['setThemeMod'][0][0] ?? null);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);

        static::assertSame(
            'var(--color--secondary)',
            $tokensWrite['component']['scope:s-header-flexible-upper']['header']['--c-header--color--surface'] ?? null,
        );
        static::assertSame(
            'var(--color--primary)',
            $tokensWrite['component']['scope:s-header']['header']['--c-header--color--surface'] ?? null,
        );
        static::assertSame(
            'var(--color--primary)',
            $tokensWrite['component']['scope:s-header-flexible-lower']['header']['--c-header--color--surface'] ?? null,
        );
        static::assertSame(
            'var(--container-width-wide)',
            $tokensWrite['component']['__general__']['header']['--c-header--container-max-width'] ?? null,
        );
    }

    #[TestDox('migrate keeps existing header color token values but updates width from legacy setting')]
    public function testMigrateKeepsExistingHeaderColorTokenValuesButUpdatesWidthFromLegacySetting(): void
    {
        $themeMods = [
            'tokens' => json_encode([
                'token' => [],
                'component' => [
                    'scope:s-header-flexible-upper' => [
                        'header' => [
                            '--c-header--color--surface' => 'var(--color--primary)',
                        ],
                    ],
                    'scope:s-header' => [
                        'header' => [
                            '--c-header--color--surface' => 'var(--color--secondary)',
                        ],
                    ],
                    'scope:s-header-flexible-lower' => [
                        'header' => [
                            '--c-header--color--surface' => 'var(--color--secondary)',
                        ],
                    ],
                    '__general__' => [
                        'header' => [
                            '--c-header--container-max-width' => '100%',
                        ],
                    ],
                ],
            ]),
            'header_background_upper' => 'secondary',
            'header_background' => 'primary',
            'header_width' => 'wide',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderAppearanceToDesignTokens($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);

        static::assertSame(
            'var(--color--primary)',
            $tokensWrite['component']['scope:s-header-flexible-upper']['header']['--c-header--color--surface'] ?? null,
        );
        static::assertSame(
            'var(--color--secondary)',
            $tokensWrite['component']['scope:s-header']['header']['--c-header--color--surface'] ?? null,
        );
        static::assertSame(
            'var(--color--secondary)',
            $tokensWrite['component']['scope:s-header-flexible-lower']['header']['--c-header--color--surface'] ?? null,
        );
        static::assertSame(
            'var(--container-width-wide)',
            $tokensWrite['component']['__general__']['header']['--c-header--container-max-width'] ?? null,
        );
    }

    #[TestDox('migrate writes full width container value for legacy fullwidth setting')]
    public function testMigrateWritesFullWidthContainerValueForLegacyFullwidthSetting(): void
    {
        $themeMods = [
            'tokens' => '{}',
            'header_background_upper' => '',
            'header_background' => '',
            'header_width' => 'fullwidth',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderAppearanceToDesignTokens($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);

        static::assertSame(
            '100%',
            $tokensWrite['component']['__general__']['header']['--c-header--container-max-width'] ?? null,
        );
    }

    #[TestDox('migrate treats legacy widde width value as wide')]
    public function testMigrateTreatsLegacyWiddeWidthValueAsWide(): void
    {
        $themeMods = [
            'tokens' => '{}',
            'header_background_upper' => '',
            'header_background' => '',
            'header_width' => 'widde',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderAppearanceToDesignTokens($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);

        static::assertSame(
            'var(--container-width-wide)',
            $tokensWrite['component']['__general__']['header']['--c-header--container-max-width'] ?? null,
        );
    }

    #[TestDox('migrate does nothing when no legacy appearance settings are present')]
    public function testMigrateDoesNothingWhenNoLegacyAppearanceSettingsArePresent(): void
    {
        $themeMods = [
            'tokens' => '{}',
            'header_background_upper' => '',
            'header_background' => '',
            'header_width' => '',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateHeaderAppearanceToDesignTokens($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
    }
}
