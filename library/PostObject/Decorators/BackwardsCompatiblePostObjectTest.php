<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BackwardsCompatiblePostObjectTest extends TestCase
{
    #[TestDox('inherits public properties from object')]
    public function testInheritsPublicPropertiesFromObject()
    {
        $legacyPost = (object) [
            'title' => 'TestTitle',
        ];

        $result = new BackwardsCompatiblePostObject($this->getPostObject(), $legacyPost);

        $this->assertEquals('TestTitle', $result->title);
    }

    #[TestDox('forwards inheritance to post object')]
    public function testForwardsGetIdToPostObject()
    {
        $postObject = $this->getPostObject();
        $postObject->expects($this->once())->method('getId')->willReturn(123);

        $result = new BackwardsCompatiblePostObject($postObject, (object) []);

        $this->assertEquals(123, $result->getId());
    }

    #[TestDox('delegates method calls to underlying post object')]
    public function testDelegatesMethodCallsToUnderlyingPostObject()
    {
        // Use reflection or direct mock configuration to add the method
        $postObjectWithMethod = $this->createMock(PostObjectInterface::class);
        $postObjectWithMethod->method('getContentHeadings')->willReturn(['heading1', 'heading2']);

        $result = new BackwardsCompatiblePostObject($postObjectWithMethod, (object) []);

        $this->assertEquals(['heading1', 'heading2'], $result->getContentHeadings());
    }

    private function getPostObject(): PostObjectInterface|MockObject
    {
        return $this->createMock(PostObjectInterface::class);
    }
}
