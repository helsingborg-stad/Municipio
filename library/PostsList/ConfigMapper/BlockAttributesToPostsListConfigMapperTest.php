<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use Municipio\PostsList\QueryVars\QueryVars;
use PHPUnit\Framework\TestCase;
use WP_Error;
use WpService\Contracts\GetTerms;

class BlockAttributesToPostsListConfigMapperTest extends TestCase
{
    public function testMapReturnsConfigDTO(): void
    {
        $wpService = new class implements GetTerms {
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|WP_Error
            {
                return [];
            }
        };
        $mapper = new BlockAttributesToPostsListConfigMapper($wpService, new QueryVars('block_'));
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
        static::assertInstanceOf(PostsListConfigDTO::class, $dto);
        static::assertInstanceOf(
            \Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface::class,
            $dto->getPostsConfig,
        );
        static::assertInstanceOf(
            \Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface::class,
            $dto->appearanceConfig,
        );
        static::assertInstanceOf(
            \Municipio\PostsList\Config\FilterConfig\FilterConfigInterface::class,
            $dto->filterConfig,
        );
        static::assertSame('block_', $dto->queryVarsPrefix);
    }
}
