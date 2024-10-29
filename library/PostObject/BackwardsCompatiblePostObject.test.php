<?php

namespace Municipio\PostObject;

use AllowDynamicProperties;
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
            'permalink' => 'http://example.com',
        ];

        $result = new BackwardsCompatiblePostObject($this->getPostObject(), $legacyPost);

        $this->assertEquals('http://example.com', $result->permalink);
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

    private function getPostObject(): PostObjectInterface|MockObject
    {
        return $this->createMock(PostObjectInterface::class);
    }
}
