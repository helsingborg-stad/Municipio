<?php

declare(strict_types=1);

namespace Municipio\Upgrade\V69;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class Version69Test extends TestCase
{
    public function testMigratesAllScopesWithoutChangingSchemeValuesAndDoesNotWriteTwice(): void
    {
        $tokens = [
            'token' => ['--color--primary' => '#123456'],
            '_dark' => ['--color--primary-contrast' => '#eeeeee'],
            '_contrast' => ['--color--primary-contrast' => '#ffffff'],
            'component' => [
                '__general__' => [
                    'footer' => [
                        '--c-footer--color--surface' => 'var(--color--primary)',
                        '--c-footer--color--surface-contrast' => '#000000',
                        '--c-footer--color' => '#000000',
                        '--c-footer--columns-count' => 4,
                    ],
                    'button' => ['--c-button--space' => 2],
                ],
                'scope:s-header-flexible-upper' => [
                    'header' => ['--c-header--color--surface' => 'var(--color--palette-4)'],
                ],
            ],
        ];
        $originalTokens = $tokens;
        $wpService = new FakeWpService([
            'getThemeMod' => static function () use (&$tokens): string {
                return json_encode($tokens, JSON_THROW_ON_ERROR);
            },
            'setThemeMod' => static function (string $name, mixed $value) use (&$tokens): bool {
                $tokens = json_decode($value, true, flags: JSON_THROW_ON_ERROR);
                return true;
            },
        ]);
        $migration = new Version69($wpService);
        $migration->upgradeToVersion();
        $migration->upgradeToVersion();

        static::assertCount(1, $wpService->methodCalls['setThemeMod']);
        static::assertSame('var(--color--primary-contrast)', $tokens['component']['__general__']['footer']['--c-footer--color--surface-contrast']);
        static::assertArrayNotHasKey('--c-footer--color', $tokens['component']['__general__']['footer']);
        static::assertSame(4, $tokens['component']['__general__']['footer']['--c-footer--columns-count']);
        static::assertSame('var(--color--palette-4-contrast)', $tokens['component']['scope:s-header-flexible-upper']['header']['--c-header--color--surface-contrast']);
        foreach (['token', '_dark', '_contrast'] as $key) {
            static::assertSame($originalTokens[$key], $tokens[$key]);
        }
        static::assertSame($originalTokens['component']['__general__']['button'], $tokens['component']['__general__']['button']);
    }

    public function testIgnoresMalformedStoredValues(): void
    {
        $wpService = new FakeWpService(['getThemeMod' => '{invalid json']);
        (new Version69($wpService))->upgradeToVersion();
        static::assertArrayNotHasKey('setThemeMod', $wpService->methodCalls);
    }
}
