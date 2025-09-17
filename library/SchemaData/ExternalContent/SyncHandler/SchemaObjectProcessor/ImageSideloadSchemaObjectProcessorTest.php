<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\SchemaObjectProcessor;

use Municipio\Schema\Schema;
use Municipio\SchemaData\ExternalContent\SyncHandler\LocalImageObjectIdGenerator\LocalImageObjectIdGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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

    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $processor = new ImageSideloadSchemaObjectProcessor($this->getWpService([]));
        $this->assertInstanceOf(ImageSideloadSchemaObjectProcessor::class, $processor);
    }

    /**
     * @testdox processes images on the "image" property
     */
    public function testProcessesImages()
    {
        $schemaObject = Schema::thing()->image([
            Schema::imageObject()
                ->url('http://example.com/image.jpg')
                ->description('An example image')
                ->name('Example Image')
        ]);

        $wpService       = $this->getWpService(['mediaSideloadImage' => 123]);
        $processor       = new ImageSideloadSchemaObjectProcessor($wpService);
        $processedObject = $processor->process($schemaObject);
        $processedImage  = $processedObject->getProperty('image')[0];

        $this->assertEquals(123, $processedImage->getProperty('@id'));
        $this->assertEquals('An example image', $processedImage->getProperty('description'));
        $this->assertEquals('Example Image', $processedImage->getProperty('name'));
    }

    /**
     * @testdox processes nested images
     */
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
        $processor       = new ImageSideloadSchemaObjectProcessor($wpService);
        $processedObject = $processor->process($schemaObject);
        $processedImage  = $processedObject->getProperty('employee')[0]->getProperty('image')[0];

        $this->assertEquals(123, $processedImage->getProperty('@id'));
        $this->assertEquals('Nested example image', $processedImage->getProperty('description'));
        $this->assertEquals('Nested Example Image', $processedImage->getProperty('name'));
    }

    /**
     * @testdox processes ImageObject that are not in an array.
     */
    public function testProcessesBareImageObject()
    {
        $schemaObject = Schema::thing()->image(
            Schema::imageObject()
                ->url('http://example.com/image.jpg')
                ->description('A bare image object')
                ->name('Bare Image Object')
        );

        $wpService       = $this->getWpService(['mediaSideloadImage' => 123]);
        $processor       = new ImageSideloadSchemaObjectProcessor($wpService);
        $processedObject = $processor->process($schemaObject);
        $processedImage  = $processedObject->getProperty('image');

        $this->assertEquals(123, $processedImage->getProperty('@id'));
        $this->assertEquals('A bare image object', $processedImage->getProperty('description'));
        $this->assertEquals('Bare Image Object', $processedImage->getProperty('name'));
    }

    /**
     * @testdox processes bare ImageObject
     */
    public function testProcessesBareImageObjectDirectly()
    {
        $schemaObject = Schema::imageObject()
            ->url('http://example.com/image.jpg')
            ->description('A bare image object')
            ->name('Bare Image Object');

        $wpService       = $this->getWpService(['mediaSideloadImage' => 123]);
        $processor       = new ImageSideloadSchemaObjectProcessor($wpService);
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
}
