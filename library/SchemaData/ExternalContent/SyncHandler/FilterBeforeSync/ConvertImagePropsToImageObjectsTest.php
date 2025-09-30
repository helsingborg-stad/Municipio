<?php

namespace Municipio\SchemaData\ExternalContent\SyncHandler\FilterBeforeSync;

use Municipio\Schema\ImageObject;
use Municipio\Schema\Schema;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class ConvertImagePropsToImageObjectsTest extends TestCase
{
    /**
     * @testdox converts image properties to ImageObject instances
     */
    public function testConvertsImagePropertiesToImageObjectInstances(): void
    {
        $schemaObjects = [Schema::thing()->image('https://example.com/image.jpg')];
        $filter        = $this->getInstance();
        $result        = $filter->convert($schemaObjects);
        $this->assertInstanceOf(ImageObject::class, $result[0]->getProperty('image'));
        $this->assertEquals('https://example.com/image.jpg', $result[0]->getProperty('image')->getProperty('url'));
    }

    /**
     * @testdox converts array of image URLs to array of ImageObject instances
     */
    public function testConvertsArrayOfImageUrlsToArrayOfImageObjectInstances(): void
    {
        $schemaObjects = [Schema::thing()->image(['https://example.com/image1.jpg', 'https://example.com/image2.jpg'])];
        $filter        = $this->getInstance();
        $result        = $filter->convert($schemaObjects);

        $this->assertInstanceOf(ImageObject::class, $result[0]->getProperty('image')[0]);
        $this->assertInstanceOf(ImageObject::class, $result[0]->getProperty('image')[1]);
        $this->assertEquals('https://example.com/image1.jpg', $result[0]->getProperty('image')[0]->getProperty('url'));
        $this->assertEquals('https://example.com/image2.jpg', $result[0]->getProperty('image')[1]->getProperty('url'));
    }

    /**
     * @testdox converts nested image properties to ImageObject instances
     */
    public function testConvertsNestedImagePropertiesToImageObjectInstances(): void
    {
        $schemaObjects = [
            Schema::event()->actor(
                Schema::person()->image('https://example.com/nested-image.jpg')
            )
        ];
        $filter        = $this->getInstance();
        $result        = $filter->convert($schemaObjects);
        $this->assertInstanceOf(ImageObject::class, $result[0]->getProperty('actor')->getProperty('image'));
        $this->assertEquals('https://example.com/nested-image.jpg', $result[0]->getProperty('actor')->getProperty('image')->getProperty('url'));
    }

    /**
     * @testdox converts nested array of image URLs to array of ImageObject instances
     */
    public function testConvertsNestedArrayOfImageUrlsToArrayOfImageObjectInstances(): void
    {
        $schemaObjects = [
            Schema::event()->actor(
                Schema::person()->image(['https://example.com/nested-image1.jpg', 'https://example.com/nested-image2.jpg'])
            )
        ];
        $filter        = $this->getInstance();
        $result        = $filter->convert($schemaObjects);

        $this->assertInstanceOf(ImageObject::class, $result[0]->getProperty('actor')->getProperty('image')[0]);
        $this->assertInstanceOf(ImageObject::class, $result[0]->getProperty('actor')->getProperty('image')[1]);
        $this->assertEquals('https://example.com/nested-image1.jpg', $result[0]->getProperty('actor')->getProperty('image')[0]->getProperty('url'));
        $this->assertEquals('https://example.com/nested-image2.jpg', $result[0]->getProperty('actor')->getProperty('image')[1]->getProperty('url'));
    }

    /**
     * @testdox does not modify if image string is not a url
     */
    public function testDoesNotModifyIfImageStringIsNotAUrl(): void
    {
        $schemaObjects = [Schema::thing()->image('Not a URL')];
        $filter        = $this->getInstance();
        $result        = $filter->convert($schemaObjects);
        $this->assertIsString($result[0]->getProperty('image'));
        $this->assertEquals('Not a URL', $result[0]->getProperty('image'));
    }

    private function getInstance(): ConvertImagePropsToImageObjects
    {
        $wpService = new class implements AddFilter {
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }
        };

        return new ConvertImagePropsToImageObjects($wpService);
    }
}
