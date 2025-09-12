<?php

namespace Municipio\SchemaData\ExternalContent\WpPostArgsFromSchemaObject;

use PHPUnit\Framework\TestCase;
use Municipio\Schema\BaseType;

class WpArgsFromSchemaObjectTest extends TestCase
{
    /**
     * @testdox array is created with title
     */
    public function testCreate()
    {
        $schemaObject  = $this->getBaseTypeInstance([ 'name' => 'Title' ]);
        $wpPostFactory = new WpPostArgsFromSchemaObject();

        $wpPost = $wpPostFactory->transform($schemaObject);

        $this->assertEquals('Title', $wpPost['post_title']);
    }

    /**
     * @testdox WP_Post is created with post_status publish
     */
    public function testCreateWithPublishStatus()
    {
        $schemaObject  = $this->getBaseTypeInstance();
        $wpPostFactory = new WpPostArgsFromSchemaObject();

        $wpPost = $wpPostFactory->transform($schemaObject,);

        $this->assertEquals('publish', $wpPost['post_status']);
    }

    private function getBaseTypeInstance(array $properties = []): BaseType
    {
        return new class ($properties) extends BaseType {
            public function __construct(array $properties)
            {
                $this->properties = $properties;
            }
        };
    }
}
