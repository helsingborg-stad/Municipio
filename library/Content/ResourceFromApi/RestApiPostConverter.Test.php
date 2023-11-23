<?php

use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\RestApiPostConverter;
use WP_Post;

class RestApiPostConverterTest extends WP_UnitTestCase
{
    public function testConvertToWPPostSetsDefaultFieldsFromApiPost()
    {
        $apiPost = $this->getMockedApiPost();
        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getName')->willReturn('post');
        $resource->method('getMediaResource')->willReturn(null);

        $converter = new RestApiPostConverter($apiPost, $resource);
        $wpPost = $converter->convertToWPPost();

        $this->assertInstanceOf(WP_Post::class, $wpPost);
        $this->assertEquals($apiPost->author, $wpPost->post_author);
        $this->assertEquals($apiPost->date, $wpPost->post_date);
        $this->assertEquals($apiPost->content->rendered, $wpPost->post_content);
        $this->assertEquals($apiPost->title->rendered, $wpPost->post_title);
        $this->assertEquals($apiPost->excerpt->rendered, $wpPost->post_excerpt);
        $this->assertEquals($apiPost->status, $wpPost->post_status);
        $this->assertEquals($apiPost->modified, $wpPost->post_modified);
        $this->assertEquals($apiPost->slug, $wpPost->post_name);
        $this->assertEquals($apiPost->guid->rendered, $wpPost->guid);
    }

    public function testConvertToWPPostConvertsIdToLocalId()
    {
        $apiPost = $this->getMockedApiPost();
        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getName')->willReturn('post');
        $resource->method('getResourceId')->willReturn(1);
        $resource->method('getMediaResource')->willReturn(null);

        $converter = new RestApiPostConverter($apiPost, $resource);
        $wpPost = $converter->convertToWPPost();

        $this->assertInstanceOf(WP_Post::class, $wpPost);
        $this->assertEquals(-11, $wpPost->ID);
    }

    public function testConvertToWPPostSetsAllNonStandardPropertiesAsMeta()
    {
        $apiPost = $this->getMockedApiPost();
        $apiPost->nonStandardProperty = 'Non standard property';
        $resource = $this->createMock(ResourceInterface::class);
        $resource->method('getName')->willReturn('post');
        $resource->method('getMediaResource')->willReturn(null);

        $converter = new RestApiPostConverter($apiPost, $resource);
        $wpPost = $converter->convertToWPPost();

        $this->assertInstanceOf(WP_Post::class, $wpPost);
        $this->assertEquals($apiPost->nonStandardProperty, $wpPost->meta->nonStandardProperty);
    }

    public function testConvertToWPPostConvertsFeaturedMediaIdToLocalMediaId()
    {
        $apiPost = $this->getMockedApiPost();
        $resource = $this->createMock(ResourceInterface::class);
        $mediaResource = $this->createMock(ResourceInterface::class);

        $mediaResource->method('getName')->willReturn('media');
        $mediaResource->method('getResourceId')->willReturn(2);
        $resource->method('getName')->willReturn('post');
        $resource->method('getMediaResource')->willReturn($mediaResource);

        $converter = new RestApiPostConverter($apiPost, $resource);
        $wpPost = $converter->convertToWPPost();

        $this->assertInstanceOf(WP_Post::class, $wpPost);
        $this->assertEquals(-22, $wpPost->meta->_thumbnail_id);
    }

    private function getMockedApiPost(): object
    {
        return (object) [
            'id' => 1,
            'title' => (object) ['rendered' => 'Title'],
            'content' => (object) ['rendered' => 'Content'],
            'excerpt' => (object) ['rendered' => 'Excerpt'],
            'date' => '2021-01-01T00:00:00',
            'modified' => '2021-01-01T00:00:00',
            'slug' => 'slug',
            'guid' => (object)['rendered' => 'https://example.com'],
            'author' => 1,
            'status' => 'publish',
            'featured_media' => 2,
            'nonStandardProperty' => 'Non standard property'
        ];
    }
}
