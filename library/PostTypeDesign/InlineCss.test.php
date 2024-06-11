<?php

namespace Municipio\PostTypeDesign;

use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\InlineCss;

class InlineCssTest extends TestCase
{
    public function testGenerateCssArrayReturnsEmptyArrayIfFaultyValues()
    {
        $InlineCssInstance = new InlineCss(['setting' => 'value'], [
            [
                'type' => 'faultyFieldType',
                'settings' => 'setting'
            ]
        ]);

        $result = $InlineCssInstance->generateCssArray();

        $this->assertEmpty($result);
    }

    public function testGenerateCssArrayReturnsArrayIfCorrectValues()
    {
        $InlineCssInstance = new InlineCss([
            'name' => [
                'color' => '#000'
            ]], 
            [[
                'type' => 'multicolor',
                'settings' => 'name',
                'choices' => [
                    'color' => 'Color'
                ],
                'output' => [
                    [
                        'choice' => 'color',
                        'element' => '.element',
                        'property' => '--color',

                    ]
                ]
            ]
        ]);

        $result = $InlineCssInstance->generateCssArray();
        
        $this->assertEquals($result, ["--color" => "#000"]);
    }

    public function testGenerateCssStringReturnsEmptyStringIfFaultyData()
    {
        $InlineCssInstance = new InlineCss([
            'name' => [
                'color' => '#000'
            ]], 
            [[
                'type' => 'faultyFieldType',
                'settings' => 'name',
            ]
        ]);

        $result = $InlineCssInstance->generateCssString();
        
        $this->assertEquals($result, '');
    }
}
