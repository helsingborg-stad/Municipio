<?php

namespace Municipio\Styleguide\Customize\TokenData;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;
use WpService\Implementations\FakeWpService;

class TokenDataTest extends TestCase
{
    #[TestDox('returns file contents as array')]
    public function testGetTokenData(): void
    {
        $tokenData = new TokenData(static::createWpService());
        $result = $tokenData->getTokenData();

        $this->assertIsArray($result);
    }

    private static function createWpService(): ApplyFilters
    {
        return new FakeWpService([
            'applyFilters' => function (string $filterName, $value) {
                return $value;
            },
        ]);
    }
}
