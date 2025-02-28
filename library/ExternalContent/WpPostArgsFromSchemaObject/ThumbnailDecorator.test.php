<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\ImageObject;
use WpService\Implementations\FakeWpService;

class ThumbnailDecoratorTest extends TestCase
{
    /**
     * @testdox Sideloads image from url
     */
    public function testSideloadImageFromUrl(): void
    {
        $url          = 'https://example.com/image.jpg';
        $wpService    = new FakeWpService(['mediaSideloadImage' => 1, 'getPosts' => [], 'updatePostMeta' => true]);
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $url);
        $decorator = new ThumbnailDecorator(new WpPostArgsFromSchemaObject('test_post_type'), $wpService);

        $post = $decorator->transform($schemaObject);

        $this->assertEquals($url, $wpService->methodCalls['mediaSideloadImage'][0][0]);
        $this->assertEquals(1, $post['meta_input']['_thumbnail_id']);
    }

    /**
     * @testdox Sideloads image from ImageObject
     */
    public function testSideloadImageFromImageObject(): void
    {
        $url           = 'https://example.com/image.jpg';
        $imageProperty = new ImageObject();
        $imageProperty->url($url);
        $wpService    = new FakeWpService(['mediaSideloadImage' => 1, 'getPosts' => [], 'updatePostMeta' => true]);
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $imageProperty);
        $decorator = new ThumbnailDecorator(new WpPostArgsFromSchemaObject('test_post_type'), $wpService);

        $post = $decorator->transform($schemaObject);

        $this->assertEquals($url, $wpService->methodCalls['mediaSideloadImage'][0][0]);
        $this->assertEquals(1, $post['meta_input']['_thumbnail_id']);
    }

    /**
     * @testdox Uses already uploaded image if found by source
     */
    public function testUsesAlreadyUploadedImageIfFound(): void
    {
        $url          = 'https://example.com/image.jpg';
        $wpService    = new FakeWpService(['getPosts' => [(object)['ID' => 123]]]);
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $url);
        $decorator = new ThumbnailDecorator(new WpPostArgsFromSchemaObject('test_post_type'), $wpService);

        $post = $decorator->transform($schemaObject);

        $this->assertArrayNotHasKey('mediaSideloadImage', $wpService->methodCalls);
        $this->assertEquals(123, $post['meta_input']['_thumbnail_id']);
    }

    /**
     * @testdox Does not connect to post image if sideload fails
     */
    public function testDoesNotConnectToPostIfSideloadFails(): void
    {
        $url          = 'https://example.com/image.jpg';
        $wpService    = new FakeWpService(['mediaSideloadImage' => WpMockFactory::createWpError(), 'getPosts' => []]);
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $url);

        $decorator = new ThumbnailDecorator(new WpPostArgsFromSchemaObject('test_post_type'), $wpService);

        $post = $decorator->transform($schemaObject);

        $this->assertArrayNotHasKey('_thumbnail_id', $post['meta_input']);
    }

    /**
     * @testdox Sets post meta for attachment that indicates that this image was downloaded during a sync.
     */
    public function testSetsMetaForAttachment(): void
    {
        $url          = 'https://example.com/image.jpg';
        $wpService    = new FakeWpService(['mediaSideloadImage' => 1, 'getPosts' => [], 'updatePostMeta' => true]);
        $decorator    = new ThumbnailDecorator(new WpPostArgsFromSchemaObject('test_post_type'), $wpService);
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $url);

        $decorator->transform($schemaObject);

        $this->assertEquals(1, $wpService->methodCalls['updatePostMeta'][0][0]);
        $this->assertEquals('synced_from_external_source', $wpService->methodCalls['updatePostMeta'][0][1]);
        $this->assertEquals(true, $wpService->methodCalls['updatePostMeta'][0][2]);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }
}
