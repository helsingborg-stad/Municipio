<?php

namespace Municipio\ExternalContent\PostsResults\Helpers;

use Municipio\ExternalContent\Sources\ISource;
use Municipio\ExternalContent\Sources\ISourceRegistry;
use PHPUnit\Framework\TestCase;
use WP_Query;

class HelpersTest extends TestCase
{
    /**
     * @testdox isQueryForExternalContent returns true if post type is in source registry
     */
    public function testIsQueryForExternalContentReturnsTrueIfPostTypeIsInSourceRegistry()
    {
        $query = $this->createMock(WP_Query::class);
        $query->method('get')->with('post_type')->willReturn('post_type_1');
        $source = $this->createMock(ISource::class);
        $source->method('getPostType')->willReturn('post_type_1');
        $sourceRegistry = $this->createMock(ISourceRegistry::class);
        $sourceRegistry->method('getSources')->willReturn([$source]);

        $helpers = new Helpers($sourceRegistry);

        $this->assertTrue($helpers->isQueryForExternalContent($query));
    }

    /**
     * @testdox isQueryForExternalContent returns false if post type on query is empty
     */
    public function testIsQueryForExternalContentReturnsFalseIfPostTypeOnQueryIsEmpty()
    {
        $query = $this->createMock(WP_Query::class);
        $query->method('get')->with('post_type')->willReturn("");
        $sourceRegistry = $this->createMock(ISourceRegistry::class);

        $helpers = new Helpers($sourceRegistry);

        $this->assertFalse($helpers->isQueryForExternalContent($query));
    }

    /**
     * @testdox isQueryForExternalContent returns false if post type is not in source registry
     */
    public function testIsQueryForExternalContentReturnsFalseIfPostTypeIsNotInSourceRegistry()
    {
        $query = $this->createMock(WP_Query::class);
        $query->method('get')->with('post_type')->willReturn('post_type_1');
        $source = $this->createMock(ISource::class);
        $source->method('getPostType')->willReturn('post_type_2');
        $sourceRegistry = $this->createMock(ISourceRegistry::class);
        $sourceRegistry->method('getSources')->willReturn([$source]);

        $helpers = new Helpers($sourceRegistry);

        $this->assertFalse($helpers->isQueryForExternalContent($query));
    }

    /**
     * @testdox getSourcesByPostType returns sources with matching post type
     */
    public function testGetSourcesByPostTypeReturnsSourcesWithMatchingPostType()
    {
        $source1 = $this->createMock(ISource::class);
        $source1->method('getPostType')->willReturn('post_type_1');
        $source2 = $this->createMock(ISource::class);
        $source2->method('getPostType')->willReturn('post_type_2');
        $sourceRegistry = $this->createMock(ISourceRegistry::class);
        $sourceRegistry->method('getSources')->willReturn([$source1, $source2]);

        $helpers = new Helpers($sourceRegistry);

        $this->assertEquals([$source1], $helpers->getSourcesByPostType('post_type_1'));
    }
}
