<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use PHPUnit\Framework\TestCase;

class BlockAttributesToPostsListConfigMapperTest extends TestCase
{
    public function testMapReturnsConfigDTO(): void
    {
        $mapper = new BlockAttributesToPostsListConfigMapper();
        $attributes = [
            'order' => 'asc',
            'orderBy' => 'date',
            'numberOfColumns' => 3,
            'design' => 'card',
            'dateFormat' => 'date',
            'dateSource' => 'post_date',
            'taxonomiesEnabledForFiltering' => [
                ['taxonomy' => 'category', 'type' => 'select'],
            ],
            'terms' => [],
            'queryVarsPrefix' => 'block_',
        ];
        $dto = $mapper->map($attributes);
        $this->assertInstanceOf(PostsListConfigDTO::class, $dto);
        $this->assertInstanceOf(
            \Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface::class,
            $dto->getPostsConfig,
        );
        $this->assertInstanceOf(
            \Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface::class,
            $dto->appearanceConfig,
        );
        $this->assertInstanceOf(
            \Municipio\PostsList\Config\FilterConfig\FilterConfigInterface::class,
            $dto->filterConfig,
        );
        $this->assertEquals('block_', $dto->queryVarsPrefix);
    }
}
