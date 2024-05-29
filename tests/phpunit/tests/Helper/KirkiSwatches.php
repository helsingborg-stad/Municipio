<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\KirkiSwatches;
use WP_Mock;
use Mockery;

/**
 * Class ListingTest
 * @group wp_mock
 */
class KirkiSwatchesTest extends TestCase
{
    /**
     * @testdox getColors returns empty array when get_theme_mod function doesn't exist
    */
    public function testKirkiSwatchesReturnsEmptyArray()
    {
        // When
        $result = KirkiSwatches::getColors();

        // Then
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * @testdox getColors returns array with set colors.
    */
    public function testKirkiSwatchesReturnsArrayWithCustomColors()
    {
        // Given
        WP_Mock::userFunction('get_theme_mod', [
        'return' =>
            [
                'base'        => 'test',
                'dark'        => 'test',
                'light'       => 'test',
                'contrasting' => 'test',
                'background'  => 'test'
            ],

        ]);
        // When
        $result = KirkiSwatches::getColors();

        // Then
        $this->assertCount(9, $result);

        foreach ($result as $color) {
            $this->assertEquals('test', $color);
        }
    }

      /**
     * @testdox getColors as default returns an array of colors.
    */
    public function testKirkiSwatches()
    {
        // Given
        WP_Mock::userFunction('get_theme_mod', []);
        // When
        $result = KirkiSwatches::getColors();

        // Then
        $this->assertCount(9, $result);

        foreach ($result as $color) {
            $isColor = strlen($color);
            $this->assertEquals('#', $color[0]);
            if (!$isColor === 4 && !$isColor === 7) {
                $this->assertTrue(false);
            }
        }
    }
}
