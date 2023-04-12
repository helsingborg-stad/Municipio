<?php

class ButtonTest extends \PHPUnit\Framework\TestCase
{
    public function testGetShapeOptionsReturnsArray()
    {
        $button = new Municipio\Customizer\Sections\Button('123');
        $this->assertIsArray($button->getShapeOptions());
    }

    public function testGetShapeOptionsContainsPill()
    {
        $button = new Municipio\Customizer\Sections\Button('123');
        $this->assertArrayHasKey('pill', $button->getShapeOptions());
    }

    public function testGetDefaultValueReturnsString()
    {
        $button = new Municipio\Customizer\Sections\Button('123');
        $this->assertIsString($button->getShapeDefaultValue());
    }

    public function testGetShapeFieldAttributesReturnsArray()
    {
        $button = new Municipio\Customizer\Sections\Button('123');
        $this->assertIsArray($button->getShapeFieldAttributes('123'));
    }

    public function testGetShapeFieldAttributesAppliesSectionID()
    {
        $sectionID = '456';
        $button = new Municipio\Customizer\Sections\Button('123');
        $attributes = $button->getShapeFieldAttributes($sectionID);

        $this->assertEquals($sectionID, $attributes['section']);
    }
}
