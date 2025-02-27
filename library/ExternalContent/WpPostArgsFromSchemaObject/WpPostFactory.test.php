<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;

class WpPostFactoryTest extends TestCase
{
    /**
     * @testdox array is created with title and content
     */
    public function testCreate()
    {
        $schemaObject  = $this->getBaseTypeInstance([ 'name' => 'Title', 'description' => 'Content', ]);
        $wpPostFactory = new WpPostFactory('test_post_type');

        $wpPost = $wpPostFactory->create($schemaObject);

        $this->assertEquals('Title', $wpPost['post_title']);
        $this->assertEquals('Content', $wpPost['post_content']);
    }

    /**
     * @testdox WP_Post is created with post_status publish
     */
    public function testCreateWithPublishStatus()
    {
        $schemaObject  = $this->getBaseTypeInstance();
        $wpPostFactory = new WpPostFactory('test_post_type');

        $wpPost = $wpPostFactory->create($schemaObject,);

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
