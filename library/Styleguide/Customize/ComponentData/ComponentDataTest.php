<?php

namespace Municipio\Styleguide\Customize\ComponentData;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;
use WpService\Implementations\FakeWpService;

class ComponentDataTest extends TestCase
{
    #[TestDox('returns file contents as array')]
    public function testGetComponentData(): void
    {
        $componentData = new ComponentData(static::createWpService());
        $result = $componentData->getComponentData();

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
