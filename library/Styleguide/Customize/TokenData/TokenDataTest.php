<?php

namespace Municipio\Styleguide\Customize\TokenData;

use Municipio\Styleguide\Customize\OverrideState\OverrideStateInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;
use WpService\Contracts\ApplyFilters;
use WpService\Implementations\FakeWpService;

class TokenDataTest extends TestCase
{
    #[TestDox('returns file contents as array')]
    public function testGetTokenData(): void
    {
        $tokenData = new TokenData(static::createWpService(), static::createOverrideStateService());
        $result = $tokenData->getTokenData();

        $this->assertIsArray($result);
    }

    private static function createWpService(): ApplyFilters&AddFilter
    {
        return new class implements ApplyFilters, AddFilter {
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }

            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }
        };
    }

    private static function createOverrideStateService(): OverrideStateInterface
    {
        return new class implements OverrideStateInterface {
            public function getOverrideState(): array
            {
                return [];
            }
        };
    }
}
