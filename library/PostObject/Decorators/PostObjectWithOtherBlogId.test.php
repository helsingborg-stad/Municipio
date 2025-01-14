<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObject;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectWithOtherBlogIdTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $decorator = new PostObjectWithOtherBlogId(new PostObject(new FakeWpService()), 2);
        $this->assertInstanceOf(PostObjectWithOtherBlogId::class, $decorator);
    }

    /**
     * @testdox blog id can be set
     */
    public function testBlogIdCanBeSet()
    {
        $decorator = new PostObjectWithOtherBlogId(new PostObject(new FakeWpService(['getCurrentBlogId' => 1])), 2);
        $this->assertEquals(2, $decorator->getBlogId());
    }
}
