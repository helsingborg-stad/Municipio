<?php

use Municipio\Customizer\Sections\Menu;

class MenuTest extends \PHPUnit\Framework\TestCase
{

    public function testGetScreenSizesReturnsArray()
    {
        $menu = new Menu('123');
        $screenSizes = $menu->getDrawerScreenSizeOptions();

        $this->assertIsArray($screenSizes);
    }

    public function testGetScreenSizesContainsExpectedValues()
    {
        $menu = new Menu('123');
        $screenSizes = $menu->getDrawerScreenSizeOptions();

        $this->assertArrayHasKey('xs', $screenSizes);
        $this->assertArrayHasKey('sm', $screenSizes);
        $this->assertArrayHasKey('md', $screenSizes);
        $this->assertArrayHasKey('lg', $screenSizes);
    }

    public function testGetDefaultDrawerScreenSizesReturnsArray()
    {
        $menu = new Menu('123');
        $defaultScreenSize = $menu->getDefaultDrawerScreenSizes();

        $this->assertIsArray($defaultScreenSize);
    }

    public function testGetDefaultDrawerScreenSizesContainsExpectedValues()
    {
        $menu = new Menu('123');
        $defaultScreenSizes = $menu->getDefaultDrawerScreenSizes();

        $this->assertContains('xs', $defaultScreenSizes);
        $this->assertContains('sm', $defaultScreenSizes);
    }

    public function testGetDrawerScreenSizesFieldArgumentsReturnsArray()
    {
        $menu = new Menu('123');
        $fieldArguments = $menu->getDrawerScreenSizesFieldArguments('456');

        $this->assertIsArray($fieldArguments);
    }

    public function testGetDrawerScreenSizesFieldArgumentsAppliesSectionID()
    {
        $menu = new Menu('123');
        $sectionID = '456';
        $fieldArguments = $menu->getDrawerScreenSizesFieldArguments($sectionID);

        $this->assertEquals($sectionID, $fieldArguments['section']);
    }

    public function testGetDrawerScreenSizesFieldArgumentsAppliesOptions()
    {
        $menu = new Menu('123');
        $options = $menu->getDrawerScreenSizeOptions();
        $defaultValues = $menu->getDefaultDrawerScreenSizes();
        $fieldArguments = $menu->getDrawerScreenSizesFieldArguments('123');

        $this->assertEquals('multicheck', $fieldArguments['type']);
        $this->assertEquals($options, $fieldArguments['choices']);
        $this->assertEquals($defaultValues, $fieldArguments['default']);
    }
}
