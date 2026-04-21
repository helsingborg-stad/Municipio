<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize\ComponentData;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\_x;
use WpService\Implementations\FakeWpService;

class ComponentDataTest extends TestCase
{
    #[TestDox('returns file contents as array')]
    public function testGetComponentData(): void
    {
        $componentData = new ComponentData(static::createWpService());
        $result = $componentData->getComponentData();

        static::assertIsArray($result);
    }

    private static function createWpService(): ApplyFilters&_x
    {
        return new FakeWpService([
            'applyFilters' => static fn (string $filterName, $value) => $value,
            '_x' => static fn (string $text, string $context, string $domain = 'default') => $text,
        ]);
    }
}
