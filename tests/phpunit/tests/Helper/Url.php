<?php

namespace Municipio\Helper\Test;

use Municipio\Helper\Url;
use WP_Mock\Tools\TestCase;

/**
 * Class UrlTest
 * @group wp_mock
 */
class UrlTest extends TestCase
{
    /**
     * @covers \Municipio\Helper\Url::getCurrent
     */
    public function testGetCurrentReturnsCurrentUrlWithoutQuerystring()
    {
        // Given
        $_SERVER['HTTP_HOST']   = 'example.com';
        $_SERVER['REQUEST_URI'] = '/page';

        // When
        $result = Url::getCurrent();

        // Then
        $this->assertEquals('//example.com/page', $result);
    }

    /**
     * @covers \Municipio\Helper\Url::getCurrent
     */
    public function testGetCurrentReturnsCurrentUrlWithQuerystring()
    {
        // Given
        $_SERVER['HTTP_HOST']   = 'example.com';
        $_SERVER['REQUEST_URI'] = '/page?param=value';

        // When
        $result = Url::getCurrent(true);

        // Then
        $this->assertEquals('//example.com/page?param=value', $result);
    }

    /**
     * @covers \Municipio\Helper\Url::getCurrent
     */
    public function testGetCurrentReturnsCurrentUrlWithoutQuerystringWhenQuerystringParameterIsFalse()
    {
        // Given
        $_SERVER['HTTP_HOST']   = 'example.com';
        $_SERVER['REQUEST_URI'] = '/page?param=value';

        // When
        $result = Url::getCurrent(false);

        // Then
        $this->assertEquals('//example.com/page', $result);
    }
}
