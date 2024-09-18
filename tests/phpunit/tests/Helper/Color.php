<?php

namespace Municipio\Tests\Helper;

use Mockery;
use WP_Mock\Tools\TestCase;
use Municipio\Helper\Color;
use phpmock\mockery\PHPMockery;

/**
 * Class ColorTest
 * @group wp_mock
 */
class ColorTest extends TestCase
{
    /**
     * @testdox prepareColor returns an rgba color when getting a correct value array.
    */
    public function testPrepareColorReturnsRgbaColor()
    {
        // When
        $result = Color::prepareColor(
            [
                'value'   => ['#000000', '0.5'],
                'default' => ['color' => '#ffffff', 'alpha' => '1'],

            ]
        );

        // Then
        $this->assertEquals('rgba(0, 0, 0, 0.5)', $result);
    }

    /**
     * @testdox prepareColor returns default values as rgba if missing value array data.
    */
    public function testPrepareColorReturnsRgbaColorBasedOnDefaultValues()
    {
        // When
        $result = Color::prepareColor(
            [
                'value'   => [],
                'default' => ['color' => '#ffffff', 'alpha' => '1'],

            ]
        );

        // Then
        $this->assertEquals('rgba(255, 255, 255, 1)', $result);
    }

    /**
     * @testdox prepareColor returns false if faulty values.
     * @dataProvider prepareColorFalseProvider
    */
    public function testPrepareColorReturnsFalseIfFaultyValues($colors)
    {
        // When
        $result = Color::prepareColor(
            $colors
        );

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getPalettes returns options if Kirki class is not found.
    */
    public function testGetPalettesReturnsArrayWithOptionsIfKirkiDoesNotExist()
    {
        // Given
        $reflection = new \ReflectionClass(Color::class);
        $namespace  = $reflection->getNamespaceName();
        PHPMockery::mock($namespace, 'class_exists')->andReturn(false);

        // When
        $result = Color::getPalettes([ 'option' ]);

        // Then
        $this->assertEquals('option', $result[0]);
    }
     /**
     * @testdox getPalettes returns array with options
    */
    public function testGetPalettesReturnsArrayWithOptions()
    {
        // Given
        $arr   = ['key1' => 'value1', 'key2' => 'value2'];
        $kirki = Mockery::mock('overload:Kirki');
        $kirki->shouldReceive('get_option')->andReturn($arr);

        // When
        $result = Color::getPalettes([ 'option' ]);

        // Then
        $this->assertEquals($arr, $result['option']);
    }

    /**
     * Provider
    */
    public function prepareColorFalseProvider()
    {
        return [
            [['value' => '']],
            [[]],
            [['default' => '']],
        ];
    }
}
