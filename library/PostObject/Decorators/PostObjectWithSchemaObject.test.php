<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObject;
use Municipio\PostObject\PostObjectInterface;
use Municipio\Schema\Schema;
use Municipio\SchemaData\SchemaObjectFromPost\SchemaObjectFromPostInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectWithSchemaObjectTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $decorator = new PostObjectWithSchemaObject($this->getPostObject(), $this->getSchemaObjectFromPostInstance());
        $this->assertInstanceOf(PostObjectWithSchemaObject::class, $decorator);
    }

    /**
     * @testdox apply() method applies schema object to post
     */
    public function testApplyMethodAppliesSchemaObjectToPost()
    {
        $schemaObject         = Schema::thing()->name('Foo');
        $schemaObjectFromPost = $this->getSchemaObjectFromPostInstance();
        $schemaObjectFromPost->method('create')->willReturn($schemaObject);

        $postObject = new PostObjectWithSchemaObject($this->getPostObject(), $schemaObjectFromPost);

        $this->assertSame('Thing', $postObject->getSchemaProperty('@type'));
        $this->assertSame('Foo', $postObject->getSchemaProperty('name'));
    }

    private function getSchemaObjectFromPostInstance(): SchemaObjectFromPostInterface|MockObject
    {
        return $this->createMock(SchemaObjectFromPostInterface::class);
    }

    private function getPostObject(): PostObjectInterface
    {
        return new PostObject(new FakeWpService());
    }
}
