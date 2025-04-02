<?php

namespace Municipio\SchemaData\SchemaObjectFromPost;

use Municipio\SchemaData\Utils\GetEnabledSchemaTypesInterface;
use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;
use Municipio\Schema\Thing;
use WP_Post;
use WpService\Implementations\FakeWpService;

class SchemaObjectWithPropertiesFromExternalContentTest extends TestCase
{
    /**
     * @testdox Sets schema property if schemaData is not empty and allowed schema types and properties are set.
     */
    public function testSetsSchemaProperty()
    {
        $wpService = new FakeWpService(['getPostMeta' => ['@type' => 'JobPosting', 'name' => 'TestSchema']]);
        $sut       = new SchemaObjectWithPropertiesFromExternalContent($wpService, $this->getEnabledSchemaTypes(), $this->schemaObjectFromPost());

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
        $sut       = new SchemaObjectWithPropertiesFromExternalContent($wpService, $this->getEnabledSchemaTypes(), $this->schemaObjectFromPost());

        $post     = (new WP_Post([]));
        $post->ID = 1;

        $schema = $sut->create($post);

        $this->assertEquals('Thing', $schema->getType());
        $this->assertNull($schema->getProperty('name'));
    }

    /**
     * @testdox Returns result from inner if schema type is not allowed.
     */
    public function testDoesNotSetSchemaPropertyIfSchemaTypeIsNotAllowed()
    {
        $wpService = new FakeWpService(['getPostMeta' => ['@type' => 'NotAllowedType', 'name' => 'TestSchema']]);
        $sut       = new SchemaObjectWithPropertiesFromExternalContent($wpService, $this->getEnabledSchemaTypes(), $this->schemaObjectFromPost());

        $post     = (new WP_Post([]));
        $post->ID = 1;

        $schema = $sut->create($post);

        $this->assertEquals('Thing', $schema->getType());
        $this->assertNull($schema->getProperty('name'));
    }

    private function getEnabledSchemaTypes(): GetEnabledSchemaTypesInterface
    {
        return new class implements GetEnabledSchemaTypesInterface {
            public function getEnabledSchemaTypesAndProperties(): array
            {
                return [
                    'JobPosting' => ['name'],
                ];
            }
        };
    }

    private function schemaObjectFromPost(): SchemaObjectFromPostInterface
    {
        return new class implements SchemaObjectFromPostInterface {
            public function create(WP_Post $post): BaseType
            {
                return new Thing();
            }
        };
    }
}
