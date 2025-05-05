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
        $innerPostObject = $this->getPostObject();
        $innerPostObject->method('getId')->willReturn(123);

        $postObject = new PostObjectWithSchemaObject($innerPostObject, $schemaObjectFromPost);

        $this->assertSame('Thing', $postObject->getSchemaProperty('@type'));
        $this->assertSame('Foo', $postObject->getSchemaProperty('name'));
    }

    /**
     * @testdox caches schema object per post ID
     */
    public function testCachesSchemaObjectPerPostId()
    {
        // Arrange
        $schemaObject         = Schema::thing()->name('Foo');
        $schemaObjectFromPost = $this->getSchemaObjectFromPostInstance();
        $innerPostObject      = $this->getPostObject();
        $innerPostObject->method('getId')->willReturn(321);
        $postObject = new PostObjectWithSchemaObject($this->getPostObject(), $schemaObjectFromPost);

        // Assert
        $schemaObjectFromPost->expects($this->once())->method('create')->willReturn($schemaObject);

        // Act
        $postObject->getSchemaProperty('@type');
        $postObject->getSchemaProperty('name');
    }

    /**
     * @testdox getSchema() returns the schema object
     */
    public function testGetSchemaReturnsTheSchemaObject()
    {
        // Arrange
        $schemaObject         = Schema::thing()->name('Foo');
        $schemaObjectFromPost = $this->getSchemaObjectFromPostInstance();
        $innerPostObject      = $this->getPostObject();
        $innerPostObject->method('getId')->willReturn(321);
        $postObject = new PostObjectWithSchemaObject($this->getPostObject(), $schemaObjectFromPost);

        // Assert
        $schemaObjectFromPost->expects($this->once())->method('create')->willReturn($schemaObject);

        // Act
        $result = $postObject->getSchema();

        // Assert
        $this->assertSame($schemaObject, $result);
    }

    private function getSchemaObjectFromPostInstance(): SchemaObjectFromPostInterface|MockObject
    {
        return $this->createMock(SchemaObjectFromPostInterface::class);
    }

    private function getPostObject(): PostObjectInterface|MockObject
    {
        return $this->createMock(PostObjectInterface::class);
    }
}
