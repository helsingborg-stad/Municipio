<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapShouldDisplayFeaturedImageTest extends TestCase
{
    #[TestDox('It should map display featured image as false when not set')]
    public function testMapShouldDisplayFeaturedImageTrue()
    {
        $mapper = new MapShouldDisplayFeaturedImage();
        $result = $mapper->map(['archiveProps' => (object) []]);
        $this->assertFalse($result);
    }

    #[TestDox('It should map display featured image as true when set to true')]
    public function testMapShouldDisplayFeaturedImageFalse()
    {
        $mapper = new MapShouldDisplayFeaturedImage();
        $result = $mapper->map(['archiveProps' => (object) ['displayFeaturedImage' => true]]);
        $this->assertTrue($result);
    }
}
