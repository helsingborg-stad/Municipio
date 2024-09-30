<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostFactory;
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
        $wpService    = new FakeWpService(['mediaSideloadImage' => 1]);
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $url);
        $decorator = new ThumbnailDecorator(new WpPostFactory(), $wpService);

        $post = $decorator->create($schemaObject, new Source('', ''));

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
        $wpService    = new FakeWpService(['mediaSideloadImage' => 1]);
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $imageProperty);
        $decorator = new ThumbnailDecorator(new WpPostFactory(), $wpService);

        $post = $decorator->create($schemaObject, new Source('', ''));

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
        $decorator = new ThumbnailDecorator(new WpPostFactory(), $wpService);

        $post = $decorator->create($schemaObject, new Source('', ''));

        $this->assertArrayNotHasKey('mediaSideloadImage', $wpService->methodCalls);
        $this->assertEquals(123, $post['meta_input']['_thumbnail_id']);
    }

    /**
     * @testdox Does not connect to post image if sideload fails
     */
    public function testDoesNotConnectToPostIfSideloadFails(): void
    {
        $url          = 'https://example.com/image.jpg';
        $wpService    = new FakeWpService(['mediaSideloadImage' => WpMockFactory::createWpError()]);
        $schemaObject = $this->getSchemaObject();
        $schemaObject->setProperty('image', $url);

        $decorator = new ThumbnailDecorator(new WpPostFactory(), $wpService);

        $post = $decorator->create($schemaObject, new Source('', ''));

        $this->assertArrayNotHasKey('_thumbnail_id', $post['meta_input']);
    }

    private function getSchemaObject(): BaseType
    {
        return new class extends BaseType {
        };
    }
}
