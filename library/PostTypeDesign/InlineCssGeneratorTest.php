<?php

namespace Municipio\PostTypeDesign;

use PHPUnit\Framework\TestCase;
use Municipio\PostTypeDesign\InlineCssGenerator;

class InlineCssGeneratorTest extends TestCase
{
    public function testGenerateCssArrayReturnsEmptyArrayIfFaultyValues()
    {
        $inlineCssGeneratorInstance = new InlineCssGenerator(['setting' => 'value'], [
            [
                'type' => 'faultyFieldType',
                'settings' => 'setting'
            ]
        ]);

        $result = $inlineCssGeneratorInstance->generateCssArray();

        $this->assertEmpty($result);
    }

    public function testGenerateCssArrayReturnsArrayIfCorrectValues()
    {
        $inlineCssGeneratorInstance = new InlineCssGenerator([
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

        $result = $inlineCssGeneratorInstance->generateCssArray();
        $this->assertEquals($result, ['.element' => ["--color" => "#000"]]);
    }

    public function testGenerateCssStringReturnsEmptyStringIfFaultyData()
    {
        $inlineCssGeneratorInstance = new InlineCssGenerator([
            'name' => [
                'color' => '#000'
            ]], 
            [[
                'type' => 'faultyFieldType',
                'settings' => 'name',
            ]
        ]);

        $result = $inlineCssGeneratorInstance->generateCssString('element');

        $this->assertEquals($result, '');
    }
}
