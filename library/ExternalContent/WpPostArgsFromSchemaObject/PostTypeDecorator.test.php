<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\BaseType;
use Spatie\SchemaOrg\Schema;

class PostTypeDecoratorTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $factory = new PostTypeDecorator('test_post_type', new WpPostFactory());
        $this->assertInstanceOf(PostTypeDecorator::class, $factory);
    }

    /**
     * @testdox applies supplied post type
     */
    public function testCreate()
    {
        $schemaObject = Schema::thing();
        $factory      = new PostTypeDecorator('test_post_type', new WpPostFactory());
        $postArgs     = $factory->create($schemaObject);

        $this->assertEquals('test_post_type', $postArgs['post_type']);
    }
}
