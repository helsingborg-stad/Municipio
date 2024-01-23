<?php

namespace Municipio\Tests\Helper;

use Mockery;
use WP_Mock;
use WP_Mock\Tools\TestCase;
use Municipio\Helper\Image;

/**
 * Class ImageTest
 * @runTestsInSeparateProcesses
 */
class ImageTest extends TestCase
{
    /**
     * @testdox resize returns false if there is no image ID or Url
     * @preserveGlobalState disabled
    */
    public function testResizeReturnsFalseIfNoImage()
    {
        // When
        $result = Image::resize(null, 100, 100);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox resize returns a resized image when a correct image ID is present.
     * @preserveGlobalState disabled
    */
    public function testResizeReturnsImageIfImageIdIsPresent()
    {
        // Given
        $this->mockUpDataForImage();

        // When
        $result = Image::resize(1, 100, 100);

        // Then
        $this->assertEquals('//test.url/test-100x100.jpg', $result);
    }

    /**
     * @testdox resize returns a resized image when a correct image URL is present.
     * @preserveGlobalState disabled
    */
    public function testResizeReturnsImageIfImageUrlIsPresent()
    {
        // Given
        $this->mockUpDataForImage();

        // When
        $result = Image::resize('https://test.url/test.jpg', 100, 100);

        // Then
        $this->assertEquals('//test.url/test-100x100.jpg', $result);
    }

    /**
     * @testdox urlToPath Null if global server variable isnt available.
     * @preserveGlobalState disabled
    */
    public function testUrlToPathReturnsNullIfGlobalServerIsNotSet()
    {
        // When
        $result = Image::urlToPath('https://test.url/test.jpg');

        // Then
        $this->assertNull($result);
    }

    /**
     * @testdox urlToPath Path url is provided.
     * @preserveGlobalState disabled
    */
    public function testUrlToPathReturnsPathIfUrlIsProvided()
    {
        // Given
        $this->mockUpDataForImage();

        // When
        $result = Image::urlToPath('https://test.url/test.jpg');

        // Then
        $this->assertEquals('root/test.jpg', $result);
    }

    /**
     * @testdox pathToUrl Returns Null if global server variable isnt available.
     * @preserveGlobalState disabled
    */
    public function testPathToUrlReturnsNullIfGlobalServerIsNotSet()
    {
        // When
        $result = Image::pathToUrl('root/test.jpg');

        // Then
        $this->assertNull($result);
    }

    /**
     * @testdox pathToUrl Returns URL when path is provided.
     * @preserveGlobalState disabled
    */
    public function testPathToUrlReturnsUrlWhenPathProvided()
    {
        // Given
        $this->mockUpDataForImage();

        // When
        $result = Image::pathToUrl('root/test.jpg');

        // Then
        $this->assertEquals('//test.url/test.jpg', $result);
    }

    /**
     * @testdox pathToUrl Returns URL when path is provided.
    */
    public function testGetImageAttachmentDataReturnsFalseIfNoImageSrc()
    {
        // Given
        WP_Mock::userFunction('wp_get_attachment_image_src', [
            'return' => false
        ]);

        // When
        $result = Image::getImageAttachmentData(1);

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox pathToUrl Returns URL when path is provided.
    */
    public function testGetImageAttachmentDataReturnsImageIfFound()
    {
        // Given
        WP_Mock::userFunction('wp_get_attachment_image_src', [
            'return' => ['0' => 'https://test.com/test.jpg']
        ]);

        WP_Mock::userFunction('get_post_meta', [
            'return' => false
        ]);

        WP_Mock::userFunction('get_the_title', [
            'return' => false
        ]);

        WP_Mock::userFunction('get_post_field', [
            'return' => false
        ]);

        // When
        $result = Image::getImageAttachmentData(1);

        // Then
        $this->assertEquals('https://test.com/test.jpg', $result['src']);
        $this->assertArrayHasKey('src', $result);
        $this->assertArrayHasKey('alt', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('caption', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('byline', $result);
    }

    /**
     * Mockup data
    */
    private function mockUpDataForImage()
    {
        WP_Mock::userFunction('wp_get_attachment_url', [
            'return' => 'https://test.url/test.jpg'
        ]);

        WP_Mock::userFunction('wp_cache_get', [
            'return' => true
        ]);

        $_SERVER                  = [];
        $_SERVER['DOCUMENT_ROOT'] = 'root';
        $_SERVER['HTTP_HOST']     = 'test.url';
    }
}
