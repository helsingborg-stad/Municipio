<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\{RestoreCurrentBlog, SwitchToBlog};
use WpService\Implementations\FakeWpService;

class PostObjectFromOtherBlogTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $this->assertInstanceOf(
            PostObjectFromOtherBlog::class,
            new PostObjectFromOtherBlog($this->getPostObject(), $this->getWpService(), 1)
        );
    }


    /**
     * @testdox getBlogId returns the provided blog id
     */
    public function testGetBlogIdReturnsTheProvidedBlogId()
    {
        $decorator = new PostObjectFromOtherBlog($this->getPostObject(), $this->getWpService(), 2);

        $this->assertEquals(2, $decorator->getBlogId());
    }

    /**
     * @testdox switches to the blog using the provided blog id when getting the value and restores the current blog after
     * @dataProvider provideFunctions
     */
    public function testFunctionSwitchesToTheBlogUsingTheProvidedBlogIdWhenGettingTheValue(string $function)
    {
        $wpService = $this->getWpService();
        $decorator = new PostObjectFromOtherBlog($this->getPostObject(), $wpService, 2);

        $decorator->{$function}();

        $this->assertCount(1, $wpService->methodCalls['switchToBlog']);
        $this->assertEquals(2, $wpService->methodCalls['switchToBlog'][0][0]);
        $this->assertCount(1, $wpService->methodCalls['restoreCurrentBlog']);
    }

    /**
     * @testdox getPermalink returns the permalink with extra query_var indicating the blog id.
     */
    public function testGetPermalinkReturnsThePermalinkWithExtraQueryVarIndicatingTheBlogId()
    {
        $postObject = $this->getPostObject();
        $postObject->method('getPermalink')->willReturn('http://example.com/?p=1');

        $wpService = $this->getWpService();
        $decorator = new PostObjectFromOtherBlog($postObject, $wpService, 2);

        $this->assertStringContainsString('blog_id=2', $decorator->getPermalink());
    }

    /**
     * Datatprovider for testFunctionSwitchesToTheBlogUsingTheProvidedBlogIdWhenGettingTheValue
     */
    public function provideFunctions(): array
    {
        return [
            'getPermalink' => ['getPermalink'],
            'getIcon'      => ['getIcon'],
        ];
    }

    private function getPostObject(): PostObjectInterface|MockObject
    {
        return $this->createStub(PostObjectInterface::class);
    }

    private function getWpService(): SwitchToBlog&RestoreCurrentBlog
    {
        return new FakeWpService(['switchToBlog' => true, 'restoreCurrentBlog' => true]);
    }
}
