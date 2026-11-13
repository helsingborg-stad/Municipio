<?php

namespace Municipio\Styleguide\Customize\OverrideState;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetThemeMod;
use WpService\Implementations\FakeWpService;

class OverrideStateTest extends TestCase
{
    #[TestDox('returns stored override state as array')]
    public function testGetOverrideState(): void
    {
        $overrideState = new OverrideState(static::createWpService());
        $result = $overrideState->getOverrideState();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('component', $result);
    }

    #[TestDox('preserves optional colour scheme maps for Design Builder')]
    public function testPreservesOptionalColourSchemes(): void
    {
        $wpService = new class implements GetThemeMod, ApplyFilters {
            public function getThemeMod(string $name, mixed $defaultValue = false): mixed
            {
                return json_encode([
                    'token' => [],
                    '_dark' => ['--color--background' => '#111111'],
                    '_contrast' => null,
                    'component' => [],
                    'componentSchemes' => [
                        'dark' => ['scope:footer' => ['footer' => ['--c-footer--color--surface' => 'var(--color--palette-1)']]],
                        'contrast' => [],
                        'unsupported' => [],
                    ],
                ]);
            }

            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };

        $result = (new OverrideState($wpService))->getOverrideState();

        static::assertSame(['--color--background' => '#111111'], $result['_dark']);
        static::assertArrayHasKey('_contrast', $result);
        static::assertNull($result['_contrast']);
        static::assertSame('var(--color--palette-1)', $result['componentSchemes']['dark']['scope:footer']['footer']['--c-footer--color--surface']);
        static::assertArrayHasKey('contrast', $result['componentSchemes']);
        static::assertArrayNotHasKey('unsupported', $result['componentSchemes']);
    }

    private static function createWpService(): GetThemeMod&ApplyFilters
    {
        return new class implements GetThemeMod, ApplyFilters {
            public function getThemeMod(string $name, mixed $defaultValue = false): mixed
            {
                return json_encode([
                    'token' => ['color-primary' => '#000000'],
                    'component' => ['button' => ['color' => '#000000']],
                ]);
            }

            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };
    }
}
