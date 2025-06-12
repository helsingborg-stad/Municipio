<?php

namespace Municipio\Tests\Helper;

use Mockery;
use WP_Mock;
use WP_Mock\Tools\TestCase;
use Municipio\Helper\Image;
use phpmock\mockery\PHPMockery;

/**
 * Class ImageTest
 * @runTestsInSeparateProcesses
 * @group wp_mock
 */
class ImageTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        WP_Mock::setUsePatchwork(true);
        WP_Mock::setUp();
    }

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
        $this->markTestIncomplete('This test requires Image to not have a direct dependency on FileHelper::fileExists. Need to refactor the code to allow for better testing.');

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
        $this->markTestIncomplete('This test requires Image to not have a direct dependency on FileHelper::fileExists. Need to refactor the code to allow for better testing.');

        // Given
        $this->mockUpDataForImage();

        // When
        $result = Image::resize('https://test.url/test.jpg', 100, 100);

        // Then
        $this->assertEquals('//test.url/test-100x100.jpg', $result);
    }

    /**
     * @testdox resize returns a original image
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testResizeReturnsOriginalImage()
    {
        // Given
        $this->mockUpDataForImage();
        Mockery::mock('alias:' . \Municipio\Helper\File::class)
            ->shouldReceive('fileExists')
            ->andReturn(true, false);

        WP_Mock::userFunction('image_make_intermediate_size', [
            'times'  => 1,
            'return' => false
        ]);

        // When
        $result = Image::resize('https://test.url/test.jpg', 100, 100);

        // Then
        $this->assertEquals('https://test.url/test.jpg', $result);
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
     * @testdox addSideloadedIdentifierToAttachment Returns WP_Error if no file found.
     */
    public function testAddSideloadedIdentifierToAttachmentReturnsWPErrorIfNoFileFound()
    {
        // Given
        WP_Mock::userFunction('get_attached_file', [
            'return' => false
        ]);
        $this->mockUpDataForImage(false);
        Mockery::mock(\WP_Error::class);

        // When
        $result = Image::addSideloadedIdentifierToAttachment(1);

        // Then
        $this->assertInstanceOf('WP_Error', $result);
    }

    /**
     * @testdox addSideloadedIdentifierToAttachment Updates post and returns nothing when file found and hash calculated.
    */
    public function testAddSideloadedIdentifierToAttachmentUpdatesPost()
    {
        // Given
        $this->mockUpDataForImage('/test/path', 'testHash');

        WP_Mock::userFunction('get_attached_file', [
            'return' => 'https://test.url/test.jpg'
        ]);

        WP_Mock::userFunction('update_post_meta', [
            'return' => true

        ]);

        // When
        $result = Image::addSideloadedIdentifierToAttachment(1);

        // Then
        $this->assertNull($result);
    }

    /**
     * @testdox getAttachmentByRemoteUrl Returns WP_Error when no download url is found.
    */
    public function testGetAttachmentByRemoteUrlReturnsWpErrorIfNoDownloadUrl()
    {
        // Given
        $wpError = Mockery::mock(\WP_Error::class);
        $this->mockedDataForGetAttachmentByRemoteUrl($wpError, true);

        // When
        $result = Image::getAttachmentByRemoteUrl(1);

        // Then
        $this->assertInstanceOf('WP_Error', $result);
    }

    /**
     * @testdox getAttachmentByRemoteUrl Returns null if no file hash.
    */
    public function testGetAttachmentByRemoteUrlReturnsNullIfNoFileHash()
    {
        // Given
        $this->mockedDataForGetAttachmentByRemoteUrl('https://test.com', false, false);

        // When
        $result = Image::getAttachmentByRemoteUrl(1);

        // Then
        $this->assertNull($result);
    }

    /**
     * @testdox getAttachmentByRemoteUrl Returns null if no posts found
    */
    public function testGetAttachmentByRemoteUrlReturnsNullIfNoPostsFound()
    {
        // Given
        $this->mockedDataForGetAttachmentByRemoteUrl(
            'https://test.com',
            false,
            "testHash",
            []
        );

        // When
        $result = Image::getAttachmentByRemoteUrl(1);

        // Then
        $this->assertNull($result);
    }

    /**
     * @testdox getAttachmentByRemoteUrl Returns first post if posts found.
    */
    public function testGetAttachmentByRemoteUrlReturnsFirstPostIfPostsFound()
    {
        // WP_Mock::bootstrap();
        // Given
        $this->mockedDataForGetAttachmentByRemoteUrl(
            'https://test.com',
            false,
            "testHash",
            [$this->getMockedPost(), $this->getMockedPost()]
        );

        // When
        $result = Image::getAttachmentByRemoteUrl(1);

        // Then
        $this->assertInstanceOf('WP_Post', $result);
    }

    /**
     * Mocked post
    */
    private function getMockedPost()
    {
        $post     = Mockery::mock('WP_Post');
        $post->ID = 1;

        return $post;
    }

    /**
     * Mockup data
    */
    private function mockedDataForGetAttachmentByRemoteUrl(
        $downloadUrl = 'test',
        $isWpError = false,
        $md5File = false,
        $getPosts = []
    ) {
        $mock = $this->mockStaticMethod('Municipio\Helper\Image', 'includeFile');
        $mock->once()->andReturn(true);

        $reflection = new \ReflectionClass(Image::class);
        $namespace  = $reflection->getNamespaceName();
        PHPMockery::mock($namespace, 'md5_file')->andReturn($md5File);

        WP_Mock::userFunction('download_url', [
            'return' => $downloadUrl
        ]);

        WP_Mock::userFunction('is_wp_error', [
            'return' => $isWpError
        ]);

        WP_Mock::userFunction('get_posts', [
            'return' => $getPosts
        ]);
    }

    /**
     * Mockup data
     */
    private function mockUpDataForImage()
    {
        WP_Mock::userFunction('update_post_meta', [
            'return' => true
        ]);

        WP_Mock::userFunction('wp_get_attachment_url', [
            'return' => 'https://test.url/test.jpg'
        ]);

        WP_Mock::userFunction('wp_cache_get', [
            'return' => true
        ]);

        $reflection = new \ReflectionClass(Image::class);
        $namespace  = $reflection->getNamespaceName();
        PHPMockery::mock($namespace, 'md5_file')->andReturn(true);

        $_SERVER                  = [];
        $_SERVER['DOCUMENT_ROOT'] = 'root';
        $_SERVER['HTTP_HOST']     = 'test.url';
    }
}
