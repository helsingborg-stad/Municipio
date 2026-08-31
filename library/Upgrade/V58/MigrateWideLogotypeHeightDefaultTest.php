<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V58;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class MigrateWideLogotypeHeightDefaultTest extends TestCase
{
    #[TestDox('migrate applies safer default when ratio is wide and legacy height is too large')]
    public function testMigrateAppliesSaferDefaultWhenRatioIsWideAndLegacyHeightIsTooLarge(): void
    {
        $themeMods = [
            'tokens' => json_encode([
                'token' => [],
                'component' => [
                    '__general__' => [
                        'header' => [
                            '--logotype-height-multiplier' => 1,
                        ],
                    ],
                ],
            ]),
            'header_logotype_height' => '5.5',
            'custom_logo' => 123,
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'getPostMeta' => static fn(int $postId, string $key = '', bool $single = false): mixed => [
                'width' => 700,
                'height' => 100,
            ],
            'setThemeMod' => true,
        ]);

        (new MigrateWideLogotypeHeightDefault($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);

        static::assertEquals(
            4.5 / 6,
            $tokensWrite['component']['__general__']['header']['--logotype-height-multiplier'] ?? null,
        );
        static::assertEquals(5.0, $tokensWrite['component']['__general__']['header']['--c-header--logotype-height-min-multiplier'] ?? null);
        static::assertEquals(5.5, $tokensWrite['component']['__general__']['header']['--c-header--logotype-height-max-multiplier'] ?? null);
    }

    #[TestDox('migrate uses legacy height when ratio is wide and legacy is reasonable')]
    public function testMigrateUsesLegacyHeightWhenRatioIsWideAndLegacyIsReasonable(): void
    {
        $themeMods = [
            'tokens' => json_encode([
                'token' => [],
                'component' => [
                    '__general__' => [
                        'header' => [],
                    ],
                ],
            ]),
            'header_logotype_height' => '4.2',
            'custom_logo' => 124,
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'getPostMeta' => static fn(int $postId, string $key = '', bool $single = false): mixed => [
                'width' => 840,
                'height' => 100,
            ],
            'setThemeMod' => true,
        ]);

        (new MigrateWideLogotypeHeightDefault($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);
        static::assertEquals(
            4.2 / 6,
            $tokensWrite['component']['__general__']['header']['--logotype-height-multiplier'] ?? null,
        );
        static::assertEquals(5.0, $tokensWrite['component']['__general__']['header']['--c-header--logotype-height-min-multiplier'] ?? null);
        static::assertEquals(5.5, $tokensWrite['component']['__general__']['header']['--c-header--logotype-height-max-multiplier'] ?? null);
    }

    #[TestDox('migrate does nothing when logotype ratio is below wide breakpoint')]
    public function testMigrateDoesNothingWhenLogotypeRatioIsBelowWideBreakpoint(): void
    {
        $themeMods = [
            'tokens' => '{}',
            'header_logotype_height' => '4.2',
            'custom_logo' => 125,
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'getPostMeta' => static fn(int $postId, string $key = '', bool $single = false): mixed => [
                'width' => 600,
                'height' => 100,
            ],
            'setThemeMod' => true,
        ]);

        (new MigrateWideLogotypeHeightDefault($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
    }

    #[TestDox('migrate normalizes legacy token key to current key when ratio is wide')]
    public function testMigrateNormalizesLegacyTokenKeyToCurrentKeyWhenRatioIsWide(): void
    {
        $themeMods = [
            'tokens' => json_encode([
                'token' => [],
                'component' => [
                    '__general__' => [
                        'header' => [
                            '--c-header--logotype-height-multiplier' => 0.75,
                            '--c-header--logotype-height' => '48px',
                        ],
                    ],
                ],
            ]),
            'header_logotype_height' => '4.5',
            'custom_logo' => 126,
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'getPostMeta' => static fn(int $postId, string $key = '', bool $single = false): mixed => [
                'width' => 770,
                'height' => 100,
            ],
            'setThemeMod' => true,
        ]);

        (new MigrateWideLogotypeHeightDefault($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);

        static::assertSame(
            0.75,
            $tokensWrite['component']['__general__']['header']['--logotype-height-multiplier'] ?? null,
        );
        static::assertEquals(5.0, $tokensWrite['component']['__general__']['header']['--c-header--logotype-height-min-multiplier'] ?? null);
        static::assertEquals(5.5, $tokensWrite['component']['__general__']['header']['--c-header--logotype-height-max-multiplier'] ?? null);
        static::assertArrayNotHasKey(
            '--c-header--logotype-height-multiplier',
            $tokensWrite['component']['__general__']['header'] ?? [],
        );
        static::assertArrayNotHasKey(
            '--c-header--logotype-height',
            $tokensWrite['component']['__general__']['header'] ?? [],
        );
    }

    #[TestDox('migrate applies safe default when ratio is unavailable and legacy value is too high')]
    public function testMigrateAppliesSafeDefaultWhenRatioIsUnavailableAndLegacyValueIsTooHigh(): void
    {
        $themeMods = [
            'tokens' => '{}',
            'header_logotype_height' => '5',
            'custom_logo' => 0,
            'logotype' => 'https://example.com/visit-logo.svg',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'attachmentUrlToPostid' => 999,
            'getPostMeta' => static fn(int $postId, string $key = '', bool $single = false): mixed => ['filesize' => 6239],
            'getAttachedFile' => false,
            'setThemeMod' => true,
        ]);

        (new MigrateWideLogotypeHeightDefault($wpService))->migrate();

        static::assertCount(1, $wpService->methodCalls['setThemeMod'] ?? []);

        $tokensWrite = json_decode((string) ($wpService->methodCalls['setThemeMod'][0][1] ?? ''), true);
        static::assertEquals(
            4.5 / 6,
            $tokensWrite['component']['__general__']['header']['--logotype-height-multiplier'] ?? null,
        );
        static::assertEquals(5.0, $tokensWrite['component']['__general__']['header']['--c-header--logotype-height-min-multiplier'] ?? null);
        static::assertEquals(5.5, $tokensWrite['component']['__general__']['header']['--c-header--logotype-height-max-multiplier'] ?? null);
    }

    #[TestDox('migrate does nothing when ratio is unavailable and legacy value is already reasonable')]
    public function testMigrateDoesNothingWhenRatioIsUnavailableAndLegacyValueIsAlreadyReasonable(): void
    {
        $themeMods = [
            'tokens' => '{}',
            'header_logotype_height' => '4.2',
            'custom_logo' => 0,
            'logotype' => 'https://example.com/visit-logo.svg',
        ];

        $wpService = new FakeWpService([
            'getThemeMod' => static fn(string $name, mixed $default = null): mixed => $themeMods[$name] ?? $default,
            'attachmentUrlToPostid' => 999,
            'getPostMeta' => static fn(int $postId, string $key = '', bool $single = false): mixed => ['filesize' => 6239],
            'getAttachedFile' => false,
            'setThemeMod' => true,
        ]);

        (new MigrateWideLogotypeHeightDefault($wpService))->migrate();

        static::assertCount(0, $wpService->methodCalls['setThemeMod'] ?? []);
    }
}