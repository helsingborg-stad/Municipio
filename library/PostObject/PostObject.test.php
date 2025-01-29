<?php

namespace Municipio\PostObject;

use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetCurrentBlogId;
use WpService\Implementations\FakeWpService;

/**
 * PostObject
 */
class PostObjectTest extends TestCase
{
    private PostObject $instance;

    protected function setUp(): void
    {
        $this->instance = new PostObject(new FakeWpService([
            'getCurrentBlogId' => 1,
        ]));
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
     * @testdox getPostType() returns an empty string
     */
    public function testGetPostTypeReturnsAnEmptyString()
    {
        $this->assertEquals('', $this->instance->getPostType());
    }

    /**
     * @testdox getIcon() returns null
     */
    public function testGetIconReturnsNull()
    {
        $this->assertNull($this->instance->getIcon());
    }

    /**
     * @testdox getBlogId() current blog id
     */
    public function testGetBlogIdReturns1()
    {
        $this->assertEquals(1, $this->instance->getBlogId());
    }
}
