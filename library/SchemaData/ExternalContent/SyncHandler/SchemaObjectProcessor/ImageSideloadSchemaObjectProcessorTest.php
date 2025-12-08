<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor;

use Municipio\Schema\Schema;
use Municipio\SchemaData\ExternalContent\SyncHandler\LocalImageObjectIdGenerator\LocalImageObjectIdGeneratorInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use wpdb;
use WpService\Contracts\{IsWpError, MediaSideloadImage, UpdatePostMeta, WpGetAttachmentUrl, WpUpdatePost};
use WpService\Implementations\FakeWpService;

class ImageSideloadSchemaObjectProcessorTest extends TestCase
{
    protected function setUp(): void
    {
        if (!defined('ABSPATH')) {
            define('ABSPATH', '');
        }
    }

    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $processor = new ImageSideloadSchemaObjectProcessor($this->getWpService([]), $this->getWpdb());
        $this->assertInstanceOf(ImageSideloadSchemaObjectProcessor::class, $processor);
    }

    #[TestDox('processes images on the "image" property')]
    public function testProcessesImages()
    {
        $schemaObject = Schema::thing()->image([
            Schema::imageObject()
                ->url('http://example.com/image.jpg')
                ->description('An example image')
                ->name('Example Image')
        ]);

        $wpService       = $this->getWpService(['mediaSideloadImage' => 123]);
        $processor       = new ImageSideloadSchemaObjectProcessor($wpService, $this->getWpdb());
        $processedObject = $processor->process($schemaObject);
        $processedImage  = $processedObject->getProperty('image')[0];

        $this->assertEquals(123, $processedImage->getProperty('@id'));
        $this->assertEquals('An example image', $processedImage->getProperty('description'));
        $this->assertEquals('Example Image', $processedImage->getProperty('name'));
    }
    
    #[TestDox('the @id property is stored as a string')]
    public function testIdPropertyIsString()
    {
        $schemaObject = Schema::thing()->image([
            Schema::imageObject()
                ->url('http://example.com/image.jpg')
                ->description('An example image')
                ->name('Example Image')
        ]);

        $wpService       = $this->getWpService(['mediaSideloadImage' => 123]);
        $processor       = new ImageSideloadSchemaObjectProcessor($wpService, $this->getWpdb());
        $processedObject = $processor->process($schemaObject);
        $processedImage  = $processedObject->getProperty('image')[0];

        $this->assertTrue('123' === $processedImage->getProperty('@id'));
    }

    #[TestDox('processes nested images')]
    public function testProcessesNestedImages()
    {
        $schemaObject = Schema::preschool()
            ->employee([
                Schema::person()
                    ->image([Schema::imageObject()
                        ->url('http://example.com/image.jpg')
                        ->description('Nested example image')
                        ->name('Nested Example Image')])
            ]);

        $wpService       = $this->getWpService(['mediaSideloadImage' => 123]);
        $processor       = new ImageSideloadSchemaObjectProcessor($wpService, $this->getWpdb());
        $processedObject = $processor->process($schemaObject);
        $processedImage  = $processedObject->getProperty('employee')[0]->getProperty('image')[0];

        $this->assertEquals(123, $processedImage->getProperty('@id'));
        $this->assertEquals('Nested example image', $processedImage->getProperty('description'));
        $this->assertEquals('Nested Example Image', $processedImage->getProperty('name'));
    }

    #[TestDox('processes ImageObject that are not in an array.')]
    public function testProcessesBareImageObject()
    {
        $schemaObject = Schema::thing()->image(
            Schema::imageObject()
                ->url('http://example.com/image.jpg')
                ->description('A bare image object')
                ->name('Bare Image Object')
        );

        $wpService       = $this->getWpService(['mediaSideloadImage' => 123]);
        $processor       = new ImageSideloadSchemaObjectProcessor($wpService, $this->getWpdb());
        $processedObject = $processor->process($schemaObject);
        $processedImage  = $processedObject->getProperty('image');

        $this->assertEquals(123, $processedImage->getProperty('@id'));
        $this->assertEquals('A bare image object', $processedImage->getProperty('description'));
        $this->assertEquals('Bare Image Object', $processedImage->getProperty('name'));
    }

    #[TestDox('processes bare ImageObject')]
    public function testProcessesBareImageObjectDirectly()
    {
        $schemaObject = Schema::imageObject()
            ->url('http://example.com/image.jpg')
            ->description('A bare image object')
            ->name('Bare Image Object');

        $wpService       = $this->getWpService(['mediaSideloadImage' => 123]);
        $processor       = new ImageSideloadSchemaObjectProcessor($wpService, $this->getWpdb());
        $processedObject = $processor->process($schemaObject);
        $processedImage  = $processedObject;

        $this->assertEquals(123, $processedImage->getProperty('@id'));
        $this->assertEquals('A bare image object', $processedImage->getProperty('description'));
        $this->assertEquals('Bare Image Object', $processedImage->getProperty('name'));
    }

    private function getWpService(
        array $methods
    ): IsWpError|MediaSideloadImage|UpdatePostMeta|WpGetAttachmentUrl|WpUpdatePost|MockObject {
        return new FakeWpService(array_merge(
            [
                'isWpError'          => false,
                'mediaSideloadImage' => 1,
                'updatePostMeta'     => true,
                'wpGetAttachmentUrl' => 'http://example.com/image.jpg',
                'wpUpdatePost'       => 1,
            ],
            $methods
        ));
    }

    private function getWpdb():wpdb {
        return new wpdb('', '', '', '');
    }
}
