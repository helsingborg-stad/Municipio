<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

class GetPostsConfigExtractorTest extends TestCase
{
    public function testExtractReturnsAllAvailableData(): void
    {
        $getPostsConfig = $this->createMock(GetPostsConfigInterface::class);
        $getPostsConfig->method('getPostTypes')->willReturn(['post', 'page']);
        $getPostsConfig->method('getPostsPerPage')->willReturn(20);
        $getPostsConfig->method('paginationEnabled')->willReturn(false);

        $extractor = new GetPostsConfigExtractor($getPostsConfig);
        $result = $extractor->extract();

        $this->assertEquals([
            'postType' => 'post',
            'postsPerPage' => 20,
            'paginationEnabled' => false,
        ], $result);
    }

    public function testExtractHandlesEmptyPostTypes(): void
    {
        $getPostsConfig = $this->createMock(GetPostsConfigInterface::class);
        $getPostsConfig->method('getPostTypes')->willReturn([]);

        $extractor = new GetPostsConfigExtractor($getPostsConfig);
        $result = $extractor->extract();

        $this->assertEquals(['postType' => null], $result);
    }

    public function testExtractHandlesMissingMethods(): void
    {
        $getPostsConfig = $this->createMock(GetPostsConfigInterface::class);

        $extractor = new GetPostsConfigExtractor($getPostsConfig);
        $result = $extractor->extract();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
