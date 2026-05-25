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
