<?php

namespace Municipio\PostObject\Icon;

use PHPUnit\Framework\TestCase;

class IconTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $icon = new Icon();
        $this->assertInstanceOf(Icon::class, $icon);
    }

    /**
     * @testdox allows setting properties from factory method
     */
    public function testAllowsSettingPropertiesFromFactoryMethod()
    {
        $icon = Icon::create([
            'size'        => 'lg',
            'label'       => 'Label',
            'icon'        => 'icon',
            'color'       => 'color',
            'customColor' => 'customColor',
            'filled'      => true,
            'decorative'  => true,
        ]);

        $this->assertSame('lg', $icon->getSize());
        $this->assertSame('Label', $icon->getLabel());
        $this->assertSame('icon', $icon->getIcon());
        $this->assertSame('color', $icon->getColor());
        $this->assertSame('customColor', $icon->getCustomColor());
        $this->assertTrue($icon->getFilled());
        $this->assertTrue($icon->getDecorative());
    }
}
