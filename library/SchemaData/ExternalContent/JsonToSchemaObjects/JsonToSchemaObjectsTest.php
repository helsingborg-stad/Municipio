<?php

namespace Municipio\SchemaData\ExternalContent\JsonToSchemaObjects;

use PHPUnit\Framework\TestCase;

class JsonToSchemaObjectsTest extends TestCase {

    /**
     * @testdox returns empty array when given an invalid JSON string
     */
    public function testTransformInvalid() {
        $json = '{ invalid json [';

        $converter = new JsonToSchemaObjects();
        $schemaObjects = $converter->transform($json);

        $this->assertEmpty($schemaObjects);
    }

    /**
     * @testdox returns empty array when given an empty JSON string
     */
    public function testTransformEmpty() {
        $json = '';

        $converter = new JsonToSchemaObjects();
        $schemaObjects = $converter->transform($json);

        $this->assertEmpty($schemaObjects);
    }

    /**
     * @testdox allows non-array JSON input
     */
    public function testTransformNonArray() {
        $json = '{"@type": "Thing", "name": "Test"}';

        $converter = new JsonToSchemaObjects();
        $schemaObjects = $converter->transform($json);

        $this->assertEquals('Test', $schemaObjects[0]->getProperty('name'));
    }
    
    /**
     * @testdox returns a Thing when given a valid JSON string containgin a Thing
     */
    public function testTransformSuccess() {
        $json = '[
            {
                "@context": "https:\/\/schema.org",
                "@type": "Thing",
                "name": "Test",
                "@id": 123
            }
        ]';

        $converter = new JsonToSchemaObjects();
        $schemaObjects = $converter->transform($json);

        $this->assertEquals('Thing', $schemaObjects[0]->getProperty('@type'));
        $this->assertEquals('Test', $schemaObjects[0]->getProperty('name'));
        $this->assertEquals(123, $schemaObjects[0]->getProperty('@id'));
    }

    /**
     * @testdox returns correct type of object when given a valid JSON string containing a different type
     */
    public function testTransformType() {
        $json = '[
            {
                "@type": "Event"
            }
        ]';

        $converter = new JsonToSchemaObjects();
        $schemaObjects = $converter->transform($json);

        $this->assertInstanceOf('Municipio\Schema\Event', $schemaObjects[0]);
    }

    /**
     * @testdox returns nested types
     */
    public function testTransformNestedTypes() {
        $json = '[
            {
                "@type": "Thing",
                "image": {
                    "@type": "ImageObject",
                    "url": "http://example.com/image.jpg"
                }
            }
        ]';

        $converter = new JsonToSchemaObjects();
        $schemaObjects = $converter->transform($json);

        $this->assertInstanceOf('Municipio\Schema\Thing', $schemaObjects[0]);
        $this->assertInstanceOf('Municipio\Schema\ImageObject', $schemaObjects[0]->getProperty('image'));
    }

    /**
     * @testdox returns nested array types
     */
    public function testTransformNestedArrayTypes() {
        $json = '[
            {
                "@type": "Thing",
                "image": [
                    {
                        "@type": "ImageObject",
                        "url": "http://example.com/image1.jpg"
                    },
                    {
                        "@type": "ImageObject",
                        "url": "http://example.com/image2.jpg"
                    }
                ]
            }
        ]';

        $converter = new JsonToSchemaObjects();
        $schemaObjects = $converter->transform($json);

        $this->assertInstanceOf('Municipio\Schema\ImageObject', $schemaObjects[0]->getProperty('image')[0]);
        $this->assertInstanceOf('Municipio\Schema\ImageObject', $schemaObjects[0]->getProperty('image')[1]);
    }
}