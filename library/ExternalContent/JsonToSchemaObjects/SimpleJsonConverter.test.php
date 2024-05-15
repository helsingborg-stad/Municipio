<?php

namespace Municipio\ExternalContent\JsonToSchemaObjects;

use PHPUnit\Framework\TestCase;

class SimpleJsonConverterTest extends TestCase {
    
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

        $converter = new SimpleJsonConverter();
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

        $converter = new SimpleJsonConverter();
        $schemaObjects = $converter->transform($json);

        $this->assertInstanceOf('Spatie\SchemaOrg\Event', $schemaObjects[0]);
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

        $converter = new SimpleJsonConverter();
        $schemaObjects = $converter->transform($json);

        $this->assertInstanceOf('Spatie\SchemaOrg\Thing', $schemaObjects[0]);
        $this->assertInstanceOf('Spatie\SchemaOrg\ImageObject', $schemaObjects[0]->getProperty('image'));
    }
}