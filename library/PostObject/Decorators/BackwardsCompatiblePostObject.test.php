<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BackwardsCompatiblePostObjectTest extends TestCase
{
    /**
     * @testdox inherits public properties from object
     */
    public function testInheritsPublicPropertiesFromObject()
    {
        $legacyPost = (object) [
            'title' => 'TestTitle',
        ];

        $result = new BackwardsCompatiblePostObject($this->getPostObject(), $legacyPost);

        $this->assertEquals('TestTitle', $result->title);
    }

    /**
     * @testdox forwards inheritance to post object
     */
    public function testForwardsGetIdToPostObject()
    {
        $postObject = $this->getPostObject();
        $postObject->expects($this->once())->method('getId')->willReturn(123);

        $result = new BackwardsCompatiblePostObject($postObject, (object) []);

        $this->assertEquals(123, $result->getId());
    }

    /**
     * @testdox does not set ->permalink from legacy post
     */
    public function testDoesNotSetPermalinkFromLegacyPost()
    {
        $legacyPost = (object) [
            'permalink' => 'http://example.com',
        ];

        $result = new BackwardsCompatiblePostObject($this->getPostObject(), $legacyPost);

        $this->assertNotEquals('http://example.com', @$result->permalink);
    }

    /**
     * @testdox ->permalink returns the result from PostObject::getPermalink()
     */
    public function testPermalinkReturnsTheResultFromPostObjectGetPermalink()
    {
        $postObject = $this->getPostObject();
        $postObject->expects($this->once())->method('getPermalink')->willReturn('http://example.com');

        $result = new BackwardsCompatiblePostObject($postObject, (object) []);

        $this->assertEquals('http://example.com', $result->permalink);
    }

    /**
     * @testdox does not allow setting permalink via magic setter
     */
    public function testDoesNotAllowSettingPermalinkViaMagicSetter()
    {
        $result = new BackwardsCompatiblePostObject($this->getPostObject(), (object) []);

        $result->permalink = 'http://example.com';

        $this->assertNotEquals('http://example.com', @$result->permalink);
    }

    /**
     * @testdox delegates method calls to underlying post object
     */
    public function testDelegatesMethodCallsToUnderlyingPostObject()
    {
        // Create a mock that has a custom method
        $postObject = $this->createMock(PostObjectInterface::class);
        
        // Use reflection or direct mock configuration to add the method
        $postObjectWithMethod = new class implements PostObjectInterface {
            public function getId(): int { return 1; }
            public function getTitle(): string { return 'Test'; }
            public function getContent(): string { return 'Content'; }
            public function getExcerpt(): string { return 'Excerpt'; }
            public function getPermalink(): string { return 'http://example.com'; }
            public function getContentHeadings(): array { return ['heading1', 'heading2']; }
            public function __get(string $name): mixed { return null; }
        };

        $result = new BackwardsCompatiblePostObject($postObjectWithMethod, (object) []);

        $this->assertEquals(['heading1', 'heading2'], $result->getContentHeadings());
    }

    /**
     * @testdox throws exception for non-existent methods
     */
    public function testThrowsExceptionForNonExistentMethods()
    {
        $postObject = $this->getPostObject();
        $result = new BackwardsCompatiblePostObject($postObject, (object) []);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method nonExistentMethod does not exist');

        $result->nonExistentMethod();
    }

    private function getPostObject(): PostObjectInterface|MockObject
    {
        return $this->createMock(PostObjectInterface::class);
    }
}
