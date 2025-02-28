<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Schema;

class PostTypeDecoratorTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $factory = new PostTypeDecorator('test_post_type', new WpPostArgsFromSchemaObject());
        $this->assertInstanceOf(PostTypeDecorator::class, $factory);
    }

    /**
     * @testdox applies supplied post type
     */
    public function testCreate()
    {
        $schemaObject = Schema::thing();
        $factory      = new PostTypeDecorator('test_post_type', new WpPostArgsFromSchemaObject());
        $postArgs     = $factory->transform($schemaObject);

        $this->assertEquals('test_post_type', $postArgs['post_type']);
    }
}
