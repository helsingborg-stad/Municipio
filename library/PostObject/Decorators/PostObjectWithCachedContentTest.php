<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectWithCachedContentTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $wpService  = new FakeWpService([]);

        $decoratedPost = new PostObjectWithCachedContent($postObject, $wpService);

        $this->assertInstanceOf(PostObjectWithCachedContent::class, $decoratedPost);
    }

    /**
     * @testdox getContent caches content per blog and post ID
     */
    public function testGetContentCachesContentPerBlogAndPostId(): void
    {
        $rawContent = '<p>Raw content</p>';

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn($rawContent);
        $postObject->method('getId')->willReturn(123);

        $wpService = new FakeWpService([
            'getCurrentBlogId' => 1
        ]);

        $decoratedPost = new PostObjectWithCachedContent($postObject, $wpService);

        // First call should fetch from the post object
        $firstCall = $decoratedPost->getContent();
        $this->assertEquals($rawContent, $firstCall);

        // Change the underlying post content
        $postObject->method('getContent')->willReturn('<p>Changed content</p>');

        // Second call should still return cached content
        $secondCall = $decoratedPost->getContent();
        $this->assertEquals($rawContent, $secondCall);
    }
}