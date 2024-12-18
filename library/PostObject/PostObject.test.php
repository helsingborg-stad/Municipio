<?php

namespace Municipio\PostObject;

use PHPUnit\Framework\TestCase;

/**
 * PostObject
 */
class PostObjectTest extends TestCase
{
    private PostObject $instance;

    protected function setUp(): void
    {
        $this->instance = new PostObject();
    }

    /**
     * @testdox getId() returns 0
     */
    public function testGetIdReturns0()
    {
        $this->assertEquals(0, $this->instance->getId());
    }

    /**
     * @testdox getTitle() returns an empty string
     */
    public function testGetTitleReturnsAnEmptyString()
    {
        $this->assertEquals('', $this->instance->getTitle());
    }

    /**
     * @testdox getPermalink() returns an empty string
     */
    public function testGetPermalinkReturnsAnEmptyString()
    {
        $this->assertEquals('', $this->instance->getPermalink());
    }

    /**
     * @testdox getCommentCount() returns 0
     */
    public function testGetCommentCountReturns0()
    {
        $this->assertEquals(0, $this->instance->getCommentCount());
    }

    /**
     * @testdox getTermIcons() returns an empty array
     */
    public function testGetTermIconsReturnsAnEmptyArray()
    {
        $this->assertEquals([], $this->instance->getTermIcons());
    }

    /**
     * @testdox getPostType() returns an empty string
     */
    public function testGetPostTypeReturnsAnEmptyString()
    {
        $this->assertEquals('', $this->instance->getPostType());
    }

    /**
     * @testdox getTermIcon() returns null
     */
    public function testGetTermIconReturnsNull()
    {
        $this->assertNull($this->instance->getTermIcon());
    }
}
