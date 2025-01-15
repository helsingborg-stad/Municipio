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
            new PostObjectFromOtherBlog($postObject, $wpService, 1)
        );
    }


    /**
     * @testdox getBlogId returns the provided blog id
     */
    public function testGetBlogIdReturnsTheProvidedBlogId()
    {
        $wpService  = new FakeWpService();
        $postObject = $this->createStub(PostObjectInterface::class);

        $decorator = new PostObjectFromOtherBlog($postObject, $wpService, 2);

        $this->assertEquals(2, $decorator->getBlogId());
    }

    /**
     * @testdox switches to the blog using the provided blog id when getting the value and restores the current blog after
     * @dataProvider provideFunctions
     */
    public function testFunctionSwitchesToTheBlogUsingTheProvidedBlogIdWhenGettingTheValue(string $function)
    {
        $wpService  = new FakeWpService(['switchToBlog' => true, 'restoreCurrentBlog' => true]);
        $postObject = $this->createStub(PostObjectInterface::class);
        $decorator  = new PostObjectFromOtherBlog($postObject, $wpService, 2);

        $decorator->{$function}();

        $this->assertCount(1, $wpService->methodCalls['switchToBlog']);
        $this->assertEquals(2, $wpService->methodCalls['switchToBlog'][0][0]);
        $this->assertCount(1, $wpService->methodCalls['restoreCurrentBlog']);
    }

    public function provideFunctions(): array
    {
        return [
            'getPermalink' => ['getPermalink'],
            'getIcon'      => ['getIcon'],
        ];
    }
}
