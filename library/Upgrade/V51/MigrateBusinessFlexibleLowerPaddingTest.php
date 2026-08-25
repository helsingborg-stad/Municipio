<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V51;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateBusinessFlexibleLowerPaddingTest extends TestCase
{
    #[TestDox('migrate writes both namespaced lower area padding tokens as global defaults')]
    public function testMigrateWritesBothNamespacedLowerAreaPaddingTokensAsGlobalDefaults(): void
    {
        $themeMods = [
            'tokens' => json_encode(['token' => [], 'component' => []]),
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateBusinessFlexibleLowerPadding($wpService))->migrate();

        $tokensWrite = $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'tokens');
        $tokensWrite = is_string($tokensWrite) ? json_decode($tokensWrite, true) : $tokensWrite;

        static::assertIsArray($tokensWrite);
        static::assertSame(
            '0',
            $tokensWrite['component']['scope:s-header-flexible-lower']['header']['--c-header--padding-x-enabled'] ?? null,
        );
        static::assertSame(
            '0',
            $tokensWrite['component']['scope:s-header-flexible-lower']['header']['--c-header--padding-y-enabled'] ?? null,
        );
    }

    #[TestDox('migrate updates missing namespaced token while preserving existing values')]
    public function testMigrateUpdatesMissingNamespacedTokenWhilePreservingExistingValues(): void
    {
        $themeMods = [
            'tokens' => json_encode([
                'token' => [],
                'component' => [
                    'scope:s-header-flexible-lower' => [
                        'header' => [
                            '--c-header--padding-y-enabled' => '1',
                        ],
                    ],
                ],
            ]),
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateBusinessFlexibleLowerPadding($wpService))->migrate();

        $tokensWrite = $this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'tokens');
        $tokensWrite = is_string($tokensWrite) ? json_decode($tokensWrite, true) : $tokensWrite;

        static::assertIsArray($tokensWrite);
        static::assertSame(
            '0',
            $tokensWrite['component']['scope:s-header-flexible-lower']['header']['--c-header--padding-x-enabled'] ?? null,
        );
        static::assertSame(
            '1',
            $tokensWrite['component']['scope:s-header-flexible-lower']['header']['--c-header--padding-y-enabled'] ?? null,
        );
    }

    #[TestDox('migrate does not write when both namespaced defaults already exist')]
    public function testMigrateDoesNotWriteWhenBothNamespacedDefaultsAlreadyExist(): void
    {
        $themeMods = [
            'tokens' => json_encode([
                'token' => [],
                'component' => [
                    'scope:s-header-flexible-lower' => [
                        'header' => [
                            '--c-header--padding-x-enabled' => '0',
                            '--c-header--padding-y-enabled' => '0',
                        ],
                    ],
                ],
            ]),
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'setThemeMod' => true,
        ]);

        (new MigrateBusinessFlexibleLowerPadding($wpService))->migrate();

        static::assertNull($this->findSetThemeModCall($wpService->methodCalls['setThemeMod'] ?? [], 'tokens'));
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
