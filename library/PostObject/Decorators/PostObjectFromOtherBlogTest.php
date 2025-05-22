<?php

namespace Municipio\PostObject\Decorators;

use Municipio\Content\PostFilters\Contracts\BlogIdQueryVar;
use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\RestoreCurrentBlog;
use WpService\Contracts\SwitchToBlog;
use WpService\Implementations\FakeWpService;

class PostObjectFromOtherBlogTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated(): void
    {
        $decorator = new PostObjectFromOtherBlog(
            $this->createPostObjectStub(),
            $this->createWpService(),
            1
        );

        $this->assertInstanceOf(PostObjectFromOtherBlog::class, $decorator);
    }

    /**
     * @testdox getBlogId returns the provided blog id
     */
    public function testGetBlogIdReturnsTheProvidedBlogId(): void
    {
        $decorator = new PostObjectFromOtherBlog(
            $this->createPostObjectStub(),
            $this->createWpService(),
            2
        );

        $this->assertEquals(2, $decorator->getBlogId());
    }

    /**
     * @testdox getPermalink returns the permalink with extra query vars indicating the blog id and the original post id.
     */
    public function testGetPermalinkAppendsBlogAndPostId(): void
    {
        $postObject = $this->createPostObjectStub();
        $postObject->method('getId')->willReturn(123);
        $postObject->method('getPermalink')->willReturn('http://example.com/hello-world/');
        $decorator = new PostObjectFromOtherBlog($postObject, $this->createWpService(), 2);

        $permalink = $decorator->getPermalink();

        $this->assertStringContainsString(BlogIdQueryVar::BLOG_ID_QUERY_VAR . '=2', $permalink);
        $this->assertStringContainsString('p=123', $permalink);
    }

    /**
     * @testdox getPermalink replaces the original site url with the current site url
     */
    public function testGetPermalinkReplacesSiteUrl(): void
    {
        $postObject = $this->createPostObjectStub();
        $postObject->method('getPermalink')->willReturn('http://other-site.com/hello-world/');
        $wpService = new FakeWpService([
            'switchToBlog'       => true,
            'restoreCurrentBlog' => true,
            'getSiteUrl'         => fn($blogId = null) => $blogId === 2 ? 'http://other-site.com' : 'http://current-site.com',
            'addQueryArg'        => fn($args, $url) => $url . '?' . http_build_query($args),
        ]);
        $decorator = new PostObjectFromOtherBlog($postObject, $wpService, 2);

        $permalink = $decorator->getPermalink();

        $this->assertStringContainsString('http://current-site.com/hello-world/', $permalink);
    }

    /**
     * Data provider for testFunctionSwitchesToTheBlogUsingTheProvidedBlogIdWhenGettingTheValue
     */
    public function provideFunctions(): array
    {
        return [
            'getPermalink' => ['getPermalink'],
            'getIcon'      => ['getIcon'],
        ];
    }

    private function createPostObjectStub(): PostObjectInterface|MockObject
    {
        return $this->createStub(PostObjectInterface::class);
    }

    private function createWpService(): SwitchToBlog&RestoreCurrentBlog
    {
        return new FakeWpService([
            'switchToBlog'       => true,
            'restoreCurrentBlog' => true,
            'getSiteUrl'         => 'http://example.com',
            'addQueryArg'        => fn($args, $url) => $url . '?' . http_build_query($args),
        ]);
    }
}
