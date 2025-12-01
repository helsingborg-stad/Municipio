<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapShouldDisplayReadingTimeTest extends TestCase
{
    #[TestDox('It should map display reading time as true when set to true')]
    public function testMapShouldDisplayReadingTimeTrue()
    {
        $mapper = new MapShouldDisplayReadingTime();
        $result = $mapper->map(['archiveProps' => (object) ['readingTime' => true]]);
        $this->assertTrue($result);
    }

    #[TestDox('It should map display reading time as false when set to false')]
    public function testMapShouldDisplayReadingTimeFalse()
    {
        $mapper = new MapShouldDisplayReadingTime();
        $result = $mapper->map(['archiveProps' => (object) ['readingTime' => false]]);
        $this->assertFalse($result);
    }
}
