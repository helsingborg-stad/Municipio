<?php

use Municipio\Customizer\Sections\Search;

class SearchTest extends \PHPUnit\Framework\TestCase
{

    public function testClassIsDefined()
    {
        $search = new Search('123');
        $this->assertInstanceOf(Search::class, $search);
    }

    public function testGetSearchFormShapeFieldAttributesReturnsArray()
    {
        $search = new Search('123');
        $this->assertIsArray($search->getSearchFormShapeFieldAttributes('123'));
    }

    public function testGetSearchFormShapeOptionsReturnsArray()
    {
        $search = new Search('123');
        $this->assertIsArray($search->getSearchFormShapeOptions());
    }

    public function testGetSearchFormShapeOptionsReturnsArrayContainingPill()
    {
        $search = new Search('123');
        $this->assertArrayHasKey('pill', $search->getSearchFormShapeOptions());
    }

    public function testGetSearchFormShapeDefaultValueReturnsString()
    {
        $search = new Search('123');
        $this->assertIsString($search->getSearchFormShapeDefaultValue());
    }

    public function testSearchFormShapeDefaultValueIsInOptions() {
        $search = new Search('123');
        $this->assertArrayHasKey($search->getSearchFormShapeDefaultValue(), $search->getSearchFormShapeOptions());
    }
}
