<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\Icons;
use WP_Mock;
use Mockery;
use tad\FunctionMocker\FunctionMocker;

/**
 * Class IconTest
 */
class IconsTest extends TestCase
{
    /**
     * @testdox getIcons returns false if missing file
    */
    public function testGetIconsReturnsFalse()
    {
        // Given
        $this->mockedData();

        // When
        $result = Icons::getIcons();

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getIcons returns an array with icon names.
    */
    public function testGetIcons()
    {
        // Given
        $this->mockedData();
        $icons           = file_get_contents(__DIR__ . '/icons.json');
        $fileExistsMock  = FunctionMocker::replace('file_exists', true);
        $fileGetContents = FunctionMocker::replace('file_get_contents', $icons);

        // When
        $result = Icons::getIcons();

        // Then
        $this->assertEquals('10k', $result[0]);
    }

    /**
     * Mocked data
    */
    private function mockedData()
    {
        define('MUNICIPIO_PATH', '/');
    }
}
