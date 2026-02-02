<?php

declare(strict_types=1);

namespace Municipio\Controller\Archive;

use PHPUnit\Framework\TestCase;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

class AsyncConfigBuilderFactoryTest extends TestCase
{
    public function testFromConfigsReturnsExpectedArray()
    {
        $appearanceConfig = $this->createMock(AppearanceConfigInterface::class);
        $appearanceConfig->method('getDateSource')->willReturn('post_date');
        $appearanceConfig->method('getDateFormat')->willReturn((object)['value' => 'date-time']);
        $appearanceConfig->method('getNumberOfColumns')->willReturn(2);

        $getPostsConfig = $this->createMock(GetPostsConfigInterface::class);
        $getPostsConfig->method('getPostsPerPage')->willReturn(5);
        $getPostsConfig->method('paginationEnabled')->willReturn(true);

        $result = AsyncConfigBuilderFactory::fromConfigs(
            'archive_',
            'archive_id',
            'post',
            $appearanceConfig,
            $getPostsConfig
        );

        $this->assertEquals([
            'queryVarsPrefix' => 'archive_',
            'id' => 'archive_id',
            'postType' => 'post',
            'dateSource' => 'post_date',
            'dateFormat' => 'date-time',
            'numberOfColumns' => 2,
            'postsPerPage' => 5,
            'paginationEnabled' => true,
        ], $result);
    }
}
