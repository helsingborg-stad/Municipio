<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapNumberOfColumnsTest extends TestCase
{
    #[TestDox('Map number of columns returns expected value')]
    public function testMapNumberOfColumnsReturnsExpectedValue()
    {
        $mapper = new MapNumberOfColumns();
        $result = $mapper->map(['archiveProps' => (object) ['numberOfColumns' => 3]]);
        $this->assertEquals(3, $result);
    }

    #[TestDox('Map number of columns returns default value when not set')]
    public function testMapNumberOfColumnsReturnsDefaultValueWhenNotSet()
    {
        $mapper = new MapNumberOfColumns();
        $result = $mapper->map(['archiveProps' => (object) []]);
        $this->assertEquals(3, $result);
    }
}
