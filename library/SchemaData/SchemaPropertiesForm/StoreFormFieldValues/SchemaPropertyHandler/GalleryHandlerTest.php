<?php

namespace Municipio\SchemaData\SchemaPropertiesForm\StoreFormFieldValues\SchemaPropertyHandler;

use Municipio\Schema\Schema;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WP_Post;
use WpService\WpService;

class GalleryHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('ARRAY_A')) {
            define('ARRAY_A', 'ARRAY_A');
        }
    }

    #[TestDox('class can be instantiated')]
    public function testClassCanBeInstantiated(): void
    {
        $handler = new GalleryHandler($this->getWpServiceMock());
        $this->assertInstanceOf(GalleryHandler::class, $handler);
    }

    #[TestDox('supports method returns true for gallery field type')]
    public function testSupportsMethodReturnsTrueForGalleryFieldType(): void
    {
        $handler = new GalleryHandler($this->getWpServiceMock());
        $result  = $handler->supports('image', 'gallery', [1, 2, 3], ['ImageObject[]']);
        $this->assertTrue($result);
    }

    #[TestDox('supports method returns false for non-gallery field type')]
    public function testSupportsMethodReturnsFalseForNonGalleryFieldType(): void
    {
        $handler = new GalleryHandler($this->getWpServiceMock());
        $result  = $handler->supports('image', 'text', [1, 2, 3], ['ImageObject[]']);
        $this->assertFalse($result);
    }

    #[TestDox('supports method returns false for non-array value')]
    public function testSupportsMethodReturnsFalseForNonArrayValue(): void
    {
        $handler = new GalleryHandler($this->getWpServiceMock());
        $result  = $handler->supports('image', 'gallery', 'not an array', ['ImageObject[]']);
        $this->assertFalse($result);
    }

    #[TestDox('supports method returns false for empty value')]
    public function testSupportsMethodReturnsFalseForEmptyValue(): void
    {
        $handler = new GalleryHandler($this->getWpServiceMock());
        $result  = $handler->supports('image', 'gallery', [], ['ImageObject[]']);
        $this->assertFalse($result);
    }

    #[TestDox('handle method sets property on schema object')]
    public function testHandleMethodSetsPropertyOnSchemaObject(): void
    {
        $wpPost             = new WP_Post([]);
        $wpPost->post_title = 'Image Title';
        $wpService          = $this->getWpServiceMock();
        $wpService->method('getPost')->willReturn($wpPost);
        $wpService->method('wpGetAttachmentImageUrl')->willReturn('http://example.com/image.jpg');
        $wpService->method('wpGetAttachmentCaption')->willReturn('Image Caption');
        $wpService->method('getPostMeta')->willReturn('Image Alt Text');
        $schemaObject = Schema::event();
        $handler      = new GalleryHandler($wpService);

        $handler->handle($schemaObject, 'image', [1, ]);

        $this->assertIsArray($schemaObject->getProperty('image'));
        $this->assertEquals('Image Title', $schemaObject->getProperty('image')[0]->getProperty('name'));
        $this->assertEquals('http://example.com/image.jpg', $schemaObject->getProperty('image')[0]->getProperty('url'));
    }

    #[TestDox('handle method does not set property if value is not numeric')]
    public function testHandleMethodDoesNotSetPropertyIfValueIsNotNumeric(): void
    {
        $wpService    = $this->getWpServiceMock();
        $schemaObject = Schema::event();
        $handler      = new GalleryHandler($wpService);

        $handler->handle($schemaObject, 'image', ['not a number']);

        $this->assertNull($schemaObject->getProperty('image'));
    }

    private function getWpServiceMock(): WpService|MockObject
    {
        return $this->createMock(WpService::class);
    }
}
