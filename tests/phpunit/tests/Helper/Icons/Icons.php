<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\Icons;
use phpmock\mockery\PHPMockery;
use ReflectionClass;

/**
 * Class IconTest
 * @group wp_mock
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
        $iconsFileContent = '["iconName"]';
        PHPMockery::mock('Municipio\Helper', "file_exists")->andReturn(true);
        PHPMockery::mock('Municipio\Helper', "file_get_contents")->andReturn($iconsFileContent);

        // When
        $result = Icons::getIcons();

        // Then
        $this->assertEquals('iconName', $result[0]);
    }

    /**
     * Mocked data
     */
    private function mockedData()
    {
        if (!defined('MUNICIPIO_PATH')) {
            define('MUNICIPIO_PATH', '/');
        }
    }
}
