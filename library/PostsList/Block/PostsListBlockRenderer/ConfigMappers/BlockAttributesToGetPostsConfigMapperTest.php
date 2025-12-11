<?php

declare(strict_types=1);

namespace Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetTerms;

class BlockAttributesToGetPostsConfigMapperTest extends TestCase
{
    #[TestDox('it maps default block attributes to get posts config correctly')]
    public function testMap(): void
    {
        $wpService = new class implements GetTerms {
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|\WP_Error
            {
                return [new \WP_Term([])];
            }
        };
        $mapper = new BlockAttributesToGetPostsConfigMapper($wpService);

        $defaultAttributes = [
            'postType' => 'custom_post',
            'postsPerPage' => floatval(5),
            'paginationEnabled' => false,
            'orderBy' => 'date',
            'order' => 'desc',
            'terms' => [
                [
                    'taxonomy' => 'category',
                    'terms' => [1, 2],
                ],
            ],
        ];

        $config = $mapper->map($defaultAttributes);

        static::assertInstanceOf(GetPostsConfigInterface::class, $config);
        static::assertSame(['custom_post'], $config->getPostTypes());
        static::assertSame(5, $config->getPostsPerPage());
        static::assertFalse($config->paginationEnabled());
        static::assertSame('date', $config->getOrderBy());
        static::assertSame(OrderDirection::DESC, $config->getOrder());
        static::assertNotEmpty($config->getTerms());
    }
}
