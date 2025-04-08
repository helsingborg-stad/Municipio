<?php

namespace Municipio\PostDecorators;

use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApplySchemaObjectTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $decorator = new ApplySchemaObject($this->getSchemaObjectFromPostInstance());
        $this->assertInstanceOf(ApplySchemaObject::class, $decorator);
    }

    private function getSchemaObjectFromPostInstance(): SchemaObjectFromPostInterface|MockObject
    {
        return $this->createMock(SchemaObjectFromPostInterface::class);
    }

    /**
     * @testdox apply() method applies schema object to post
     */
    public function testApplyMethodAppliesSchemaObjectToPost()
    {
        $post                 = new \WP_Post([]);
        $schemaObjectFromPost = $this->getSchemaObjectFromPostInstance();
        $schemaObjectFromPost->method('create')->willReturn(Schema::thing());

        $decorator     = new ApplySchemaObject($schemaObjectFromPost);
        $decoratedPost = $decorator->apply($post);

        $this->assertSame('Thing', $decoratedPost->schemaObject->getType());
    }
}
