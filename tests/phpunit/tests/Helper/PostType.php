<?php

namespace Municipio\Tests\Helper;

use WP_Mock\Tools\TestCase;
use Municipio\Helper\PostType;
use WP_Mock;
use Mockery;

/**
 * Class PostTypeTest
 * @group wp_mock
 */
class PostTypeTest extends TestCase
{
    /**
     * @testdox getPublic returns empty array when no post types are found
    */
    public function testGetPublicReturnsEmptyArray()
    {
        // Given
        $this->mockedGetPublicFunctions([]);

        // When
        $result = PostType::getPublic([]);

        // Then
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * @testdox getPublic returns array of WP_Post_Type filtering filtering out page by default.
    */
    public function testGetPublicReturnsArrayOfObjects()
    {
        // Given
        $this->mockedGetPublicFunctions();

        // When
        $result = PostType::getPublic([]);

        // Then
        $this->assertInstanceOf('WP_Post_Type', $result['test']);
        $this->assertInstanceOf('WP_Post_Type', $result['mod-test']);
        $this->assertInstanceOf('WP_Post_Type', $result['page']);
    }

    /**
     * @testdox getPublic returns filtered array based on parameter.
     * Removes "test" based on parameter.
     * Removes "mod-test" as it starts with "-mod".
    */
    public function testGetPublicReturnsFilteredPosts()
    {
        // Given
        $this->mockedGetPublicFunctions();

        // When
        $result = PostType::getPublic(['test']);

        // Then
        $this->assertArrayNotHasKey('mod-test', $result);
        $this->assertArrayNotHasKey('test', $result);
        $this->assertArrayHasKey('page', $result);
        // $this->assertEmpty($result);
    }

    /**
     * @testdox getPostType returns an empty object if no available post type.
    */
    public function testPostTypeDetailsReturnsEmptyObject()
    {
        // Given
        $this->mockedUserFunctions();

        // When
        $result = PostType::postTypeDetails();

        // Then
        $this->assertFalse($result);
    }

    /**
     * @testdox getPostType returns camelCased object.
    */
    public function testPostTypeDetails()
    {
        // Given
        $this->mockedUserFunctions('test', $this->mockPostTypeObject('test'));

        // When
        $result = PostType::postTypeDetails();

        // Then
        $this->assertObjectHasProperty('camelCase', $result);
    }

    /**
     * @testdox postTypeRestUrl returns null if no post type is found.
    */
    public function testPostTypeRestUrlReturnsNullIfNoPostType()
    {
        // Given
        $this->mockedUserFunctions();

        // When
        $result = PostType::postTypeRestUrl();

        // Then
        $this->assertNull($result);
    }

    /**
     * @testdox postTypeRestUrl returns url based on page post type.
    */
    public function testPostTypeRestUrlReturnsUrlBasedOnPagePostType()
    {
        // Given
        $this->mockedUserFunctions('test', $this->mockPostTypeObject('test'));

        // When
        $result = PostType::postTypeRestUrl();

        // Then
        $this->assertEquals('https://test.com/wp/v2/test', $result);
    }

    /**
     * @testdox postTypeRestUrl returns url based on page post type.
    */
    public function testPostTypeRestUrl()
    {
        // Given
        WP_Mock::userFunction('get_post_type', [
            'times' => 0
        ]);

        WP_Mock::userFunction('get_post_type_object', [
            'times'  => 1,
            'return' => $this->mockPostTypeObject()
        ]);

        WP_Mock::userFunction('get_rest_url', [
            'return' => 'https://test.com/'
        ]);

        // When
        $result = PostType::postTypeRestUrl('test');

        // Then
        $this->assertEquals('https://test.com/wp/v2/test', $result);
    }

    /**
     * Mocked post type
    */
    private function mockPostTypeObject($name = 'test')
    {
        $postTypeMock               = Mockery::mock('WP_Post_Type');
        $postTypeMock->public       = true;
        $postTypeMock->name         = $name;
        $postTypeMock->camel_case   = 'camelCase';
        $postTypeMock->show_in_rest = true;
        $postTypeMock->rest_base    = 'test';

        return $postTypeMock;
    }

    /**
     * Mocked GetPublic functions
    */
    private function mockedGetPublicFunctions($postTypes = null)
    {
        WP_Mock::userFunction('get_post_types', [
            'args'   => [
                ['public' => true],
                'object'
            ],
            'return' => $postTypes ?? [
                'test'     => $this->mockPostTypeObject('test'),
                'page'     => $this->mockPostTypeObject('page'),
                'mod-test' => $this->mockPostTypeObject('mod-test')
            ]
        ]);
    }

    /**
     * Mocked PostTypeObject functions
    */
    private function mockedUserFunctions($getPostType = false, $getPostTypeObject = null)
    {
        WP_Mock::userFunction('get_post_type', [
            'return' => $getPostType,
        ]);

        WP_Mock::userFunction('get_post_type_object', [
            'return' => $getPostTypeObject,
        ]);

        WP_Mock::userFunction('get_rest_url', [
            'return' => 'https://test.com/'
        ]);
    }
}
