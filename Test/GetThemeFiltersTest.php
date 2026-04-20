<?php

namespace Municipio\Test;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetThemeFiltersTest extends TestCase
{
    use GetThemeFilters;

    #[TestDox('can get theme filters')]
    public function testGetThemeFilters(): void
    {
        $filters = static::getThemeFilters();
        $this->assertIsArray($filters);
        $this->assertNotEmpty($filters);
    }
}
