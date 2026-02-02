<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;

class PostsListDataExtractorTest extends TestCase
{
    public function testExtractReturnsIdAndAsyncId(): void
    {
        $postsListData = ['id' => 'test_id', 'other' => 'data'];

        $extractor = new PostsListDataExtractor($postsListData);
        $result = $extractor->extract();

        $this->assertEquals([
            'id' => 'test_id',
            'asyncId' => 'test_id',
        ], $result);
    }

    public function testExtractHandlesMissingId(): void
    {
        $postsListData = ['other' => 'data'];

        $extractor = new PostsListDataExtractor($postsListData);
        $result = $extractor->extract();

        $this->assertEquals([
            'id' => null,
            'asyncId' => null,
        ], $result);
    }

    public function testExtractHandlesEmptyArray(): void
    {
        $postsListData = [];

        $extractor = new PostsListDataExtractor($postsListData);
        $result = $extractor->extract();

        $this->assertEquals([
            'id' => null,
            'asyncId' => null,
        ], $result);
    }
}
