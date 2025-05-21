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
     * @testdox getPermalink returns the permalink with extra query vars indicating the blog id and the original post id.
     */
    public function testGetPermalinkReturnsThePermalinkWithExtraQueryVarIndicatingTheBlogId()
    {
        $postObject = $this->getPostObject();
        $postObject->method('getId')->willReturn(123);
        $postObject->method('getPermalink')->willReturn('http://example.com/hello-world/');

        $wpService = $this->getWpService();
        $decorator = new PostObjectFromOtherBlog($postObject, $wpService, 2);

        $this->assertStringContainsString('blog_id=2', $decorator->getPermalink());
        $this->assertStringContainsString('p=123', $decorator->getPermalink());
    }

    /**
     * @testdox getPermalink replaces the original site url with the current site url
     */
    public function testGetPermalinkReplacesTheOriginalSiteUrlWithTheCurrentSiteUrl()
    {
        $postObject = $this->getPostObject();
        $postObject->method('getPermalink')->willReturn('http://other-site.com/hello-world/');
        $wpService = new FakeWpService([
            'switchToBlog'       => true,
            'restoreCurrentBlog' => true,
            'getSiteUrl'         => fn($blogId = null) => $blogId === 2 ? 'http://other-site.com' : 'http://current-site.com',
        ]);

        $decorator = new PostObjectFromOtherBlog($postObject, $wpService, 2);

        $this->assertStringContainsString('http://current-site.com/hello-world/', $decorator->getPermalink());
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
        return new FakeWpService(['switchToBlog' => true, 'restoreCurrentBlog' => true, 'getSiteUrl' => 'http://example.com']);
    }
}
