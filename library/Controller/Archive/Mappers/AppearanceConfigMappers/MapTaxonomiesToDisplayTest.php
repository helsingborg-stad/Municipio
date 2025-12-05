<?php

namespace Municipio\Controller\Archive\Mappers\AppearanceConfigMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MapTaxonomiesToDisplayTest extends TestCase
{
    #[TestDox('It should return an empty array when no taxonomies are set')]
    public function testMapTaxonomiesReturnsExpectedArray()
    {
        $mapper = new MapTaxonomiesToDisplay();
        $result = $mapper->map(['archiveProps' => (object) []]);
        $this->assertEquals([], $result);
    }

    #[TestDox('It should return the correct taxonomies array when taxonomies are set')]
    public function testMapTaxonomiesReturnsSetArray()
    {
        $mapper   = new MapTaxonomiesToDisplay();
        $expected = ['category', 'tag'];
        $result   = $mapper->map(['archiveProps' => (object) ['taxonomiesToDisplay' => $expected]]);
        $this->assertEquals($expected, $result);
    }

    #[TestDox('It handle empty string input for taxonomiesToDisplay by converting it to an empty array')]
    public function testMapTaxonomiesHandlesEmptyStringInput()
    {
        $mapper = new MapTaxonomiesToDisplay();
        $result = $mapper->map(['archiveProps' => (object) ['taxonomiesToDisplay' => '']]);
        $this->assertEquals([], $result);
    }
}
