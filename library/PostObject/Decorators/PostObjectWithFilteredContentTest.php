<?php

namespace Municipio\PostObject\Decorators;

use Municipio\PostObject\PostObjectInterface;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PostObjectWithFilteredContentTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated(): void
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $wpService = new FakeWpService([]);

        $decoratedPost = new PostObjectWithFilteredContent($postObject, $wpService);

        $this->assertInstanceOf(PostObjectWithFilteredContent::class, $decoratedPost);
    }

    /**
     * @testdox getContent applies WordPress content filters
     */
    public function testGetContentAppliesWordPressContentFilters(): void
    {
        $rawContent = '<p>Raw content</p>';
        $filteredContent = '<p class="filtered">Raw content</p>';

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn($rawContent);

        $wpService = new FakeWpService([
            'applyFilters' => function ($hook, $content) use ($filteredContent) {
                if ($hook === 'the_content') {
                    return $filteredContent;
                }
                return $content;
            }
        ]);

        $decoratedPost = new PostObjectWithFilteredContent($postObject, $wpService);
        $result = $decoratedPost->getContent();

        $this->assertEquals($filteredContent, $result);
    }

    /**
     * @testdox getTitle applies WordPress title filters
     */
    public function testGetTitleAppliesWordPressTitleFilters(): void
    {
        $rawTitle = 'Raw Title';
        $filteredTitle = 'Filtered Title';

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getTitle')->willReturn($rawTitle);
        $postObject->method('getId')->willReturn(123);

        $wpService = new FakeWpService([
            'applyFilters' => function ($hook, $title, $id) use ($filteredTitle) {
                if ($hook === 'the_title' && $id === 123) {
                    return $filteredTitle;
                }
                return $title;
            }
        ]);

        $decoratedPost = new PostObjectWithFilteredContent($postObject, $wpService);
        $result = $decoratedPost->getTitle();

        $this->assertEquals($filteredTitle, $result);
    }

    /**
     * @testdox content with more tag is split correctly
     */
    public function testContentWithMoreTagIsSplitCorrectly(): void
    {
        $rawContent = '<p>Excerpt content</p><!--more--><p>Full content</p>';

        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getContent')->willReturn($rawContent);

        $wpService = new FakeWpService([
            'applyFilters' => function ($hook, $content) {
                // Mock the_excerpt and the_content filters
                if ($hook === 'the_excerpt') {
                    return '<p class="lead">Excerpt content</p>';
                }
                if ($hook === 'the_content') {
                    return '<p class="content">Full content</p>';
                }
                return $content;
            }
        ]);

        $decoratedPost = new PostObjectWithFilteredContent($postObject, $wpService);
        $result = $decoratedPost->getContent();

        $this->assertStringContains('<p class="lead">Excerpt content</p>', $result);
        $this->assertStringContains('<p class="content">Full content</p>', $result);
    }

    /**
     * @testdox passes through other PostObject methods
     */
    public function testPassesThroughOtherPostObjectMethods(): void
    {
        $postObject = $this->createMock(PostObjectInterface::class);
        $postObject->method('getId')->willReturn(123);
        $postObject->method('getPermalink')->willReturn('http://example.com/test');

        $wpService = new FakeWpService([]);

        $decoratedPost = new PostObjectWithFilteredContent($postObject, $wpService);

        $this->assertEquals(123, $decoratedPost->getId());
        $this->assertEquals('http://example.com/test', $decoratedPost->getPermalink());
    }
}