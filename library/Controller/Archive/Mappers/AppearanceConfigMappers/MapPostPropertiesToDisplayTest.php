<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapPostPropertiesToDisplayTest extends TestCase
{
    #[TestDox('returns properties to display if set')]
    public function testMapPostPropertiesReturnsExpectedArray()
    {
        $mapper = new MapPostPropertiesToDisplay();
        $result = $mapper->map(['archiveProps' => (object) ['postPropertiesToDisplay' => ["title", "date"]]]);
        $this->assertEquals(["title", "date"], $result);
    }

    #[TestDox('returns empty array if properties to display is not set')]
    public function testMapPostPropertiesReturnsEmptyArrayWhenNotSet()
    {
        $mapper = new MapPostPropertiesToDisplay();
        $result = $mapper->map(['archiveProps' => (object) []]);
        $this->assertEquals([], $result);
    }
}
