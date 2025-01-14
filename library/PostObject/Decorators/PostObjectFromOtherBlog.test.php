<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectFromOtherBlogTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $wpService  = new FakeWpService();
        $postObject = $this->createStub(PostObjectInterface::class);

        $this->assertInstanceOf(
            PostObjectFromOtherBlog::class,
            new PostObjectFromOtherBlog($postObject, $wpService)
        );
    }

    /**
     * @testdox getIcon() performs a switch to the correct blog if the post is from another blog
     */
    public function testGetIconSwitchesToCorrectBlog()
    {
        $wpService  = new FakeWpService(['isMultisite' => true, 'getCurrentBlogId' => 1, 'switchToBlog' => true, 'restoreCurrentBlog' => true]);
        $postObject = $this->createStub(PostObjectInterface::class);
        $postObject->method('getBlogId')->willReturn(2);
        $decoratedPostObject = new PostObjectFromOtherBlog($postObject, $wpService);

        $decoratedPostObject->getIcon();

        $this->assertEquals(2, $wpService->methodCalls['switchToBlog'][0][0]);
    }
}
