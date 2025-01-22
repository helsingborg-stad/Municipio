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

    private function getPostObject(): PostObjectInterface|MockObject
    {
        return $this->createMock(PostObjectInterface::class);
    }
}
