<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;
use Municipio\Schema\Contracts\GeoCoordinatesContract;
use Municipio\Schema\Thing;
use Municipio\SchemaData\SchemaPropertyValueSanitizer\SchemaPropertyValueSanitizer;
use Municipio\SchemaData\Utils\GetSchemaPropertiesWithParamTypes;
use PHPUnit\Framework\MockObject\MockObject;
use WP_Post;
use WpService\Implementations\FakeWpService;

class SchemaObjectFromPostMetaTest extends TestCase
{
    /**
     * @testdox Sets schema property if schemaData is not empty and allowed schema types and properties are set.
     */
    public function testSetsSchemaProperty()
    {
        $wpService = new FakeWpService(['getPostMeta' => ['@type' => 'JobPosting', 'name' => 'TestSchema']]);
        $sut       = new SchemaObjectFromPostMeta(
            $wpService,
            $this->schemaObjectFromPost(),
            $this->getSchemaPropertiesWithParamTypes(),
            $this->getSchemaPropertyValueSanitizer()
        );

        $post     = (new WP_Post([]));
        $post->ID = 1;

        $schema = $sut->create($post);

        $this->assertEquals('JobPosting', $schema->getType());
        $this->assertEquals('TestSchema', $schema->getProperty('name'));
    }

    /**
     * @testdox Returns result from inner if schemaData is empty.
     */
    public function testDoesNotSetSchemaPropertyIfSchemaDataIsEmpty()
    {
        $wpService = new FakeWpService(['getPostMeta' => []]);
        $sut       = new SchemaObjectFromPostMeta(
            $wpService,
            $this->schemaObjectFromPost(),
            $this->getSchemaPropertiesWithParamTypes(),
            $this->getSchemaPropertyValueSanitizer()
        );

        $post     = (new WP_Post([]));
        $post->ID = 1;

        $schema = $sut->create($post);

        $this->assertEquals('Thing', $schema->getType());
        $this->assertNull($schema->getProperty('name'));
    }

    /**
     * @testdox sanitizes nested schemaData properties from array
     */
    public function testSanitizesNestedSchemaDataPropertiesFromArray()
    {
        $wpService = new FakeWpService(['getPostMeta' => ['@type' => 'Place', 'geo' => ['@type' => 'GeoCoordinates', 'latitude' => 0, 'longitude' => 0]]]);
        $sut       = new SchemaObjectFromPostMeta(
            $wpService,
            $this->schemaObjectFromPost(),
            $this->getSchemaPropertiesWithParamTypes(),
            $this->getSchemaPropertyValueSanitizer()
        );

        $post     = (new WP_Post([]));
        $post->ID = 1;

        $schema = $sut->create($post);

        $this->assertInstanceOf(GeoCoordinatesContract::class, $schema->getProperty('geo'));
    }

    /**
     * @testdox sanitizes nested schemaData properties from serialized array
     */
    public function testSanitizesNestedSchemaDataPropertiesFromSerializedArray()
    {
        $geoCoordinates = serialize([ '@type' => 'GeoCoordinates', 'latitude' => 0, 'longitude' => 0, ]);
        $wpService      = new FakeWpService(['getPostMeta' => ['@type' => 'Place', 'geo' => $geoCoordinates]]);
        $sut            = new SchemaObjectFromPostMeta(
            $wpService,
            $this->schemaObjectFromPost(),
            $this->getSchemaPropertiesWithParamTypes(),
            $this->getSchemaPropertyValueSanitizer()
        );

        $post     = (new WP_Post([]));
        $post->ID = 1;

        $schema = $sut->create($post);

        $this->assertInstanceOf(GeoCoordinatesContract::class, $schema->getProperty('geo'));
    }

    /**
     * @testdox array of description strings or TextObjects are sanitized correctly
     */
    public function testSanitizesArrayOfDescriptionStringsOrTextObjects()
    {
        $description = [
            "<strong>Lorem ipsum dolor sit amet</strong> <i>consectetur adipiscing elit</i>. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. <a href=\"https://greenwood-elementary.example.com\">Visit our website</a> <strong>Pulvinar vivamus fringilla lacus nec metus bibendum egestas</strong>. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.",
            "Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere. Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos himenaeos.",
            [
                '@type' => 'TextObject',
                'name'  => 'About Greenwood Elementary School',
                'text'  => 'Greenwood Elementary School is committed to providing a safe and nurturing environment where students can thrive academically, socially, and emotionally.'
            ]
        ];

        $wpService = new FakeWpService(['getPostMeta' => ['@type' => 'ElementarySchool', 'description' => $description]]);
        $sut       = new SchemaObjectFromPostMeta(
            $wpService,
            $this->schemaObjectFromPost(),
            $this->getSchemaPropertiesWithParamTypes(),
            $this->getSchemaPropertyValueSanitizer()
        );

        $post     = (new WP_Post([]));
        $post->ID = 1;

        $schema = $sut->create($post);

        $this->assertIsArray($schema->getProperty('description'));
        $this->assertCount(3, $schema->getProperty('description'));
        $this->assertEquals('ElementarySchool', $schema->getType());
        $this->assertEquals('About Greenwood Elementary School', $schema->getProperty('description')[2]->getProperty('name'));
        $this->assertEquals('Greenwood Elementary School is committed to providing a safe and nurturing environment where students can thrive academically, socially, and emotionally.', $schema->getProperty('description')[2]->getProperty('text'));
    }

    private function schemaObjectFromPost(): SchemaObjectFromPostInterface
    {
        return new class implements SchemaObjectFromPostInterface {
            public function create(WP_Post|PostObjectInterface $post): BaseType
            {
                return new Thing();
            }
        };
    }

    private function getSchemaPropertiesWithParamTypes(): GetSchemaPropertiesWithParamTypes|MockObject
    {
        $mock = $this->createMock(GetSchemaPropertiesWithParamTypes::class);
        $mock->method('getSchemaPropertiesWithParamTypes')->willReturn([]);

        return $mock;
    }

    private function getSchemaPropertyValueSanitizer(): SchemaPropertyValueSanitizer|MockObject
    {
        $mock = $this->createMock(SchemaPropertyValueSanitizer::class);
        $mock->method('sanitize')->willReturnCallback(fn ($value, $allowedTypes) => $value);

        return $mock;
    }
}
