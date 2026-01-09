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

        $config = $mapper->map(static::getDefaultAttributes());

        static::assertInstanceOf(GetPostsConfigInterface::class, $config);
        static::assertSame(['custom_post'], $config->getPostTypes());
        static::assertSame(5, $config->getPostsPerPage());
        static::assertFalse($config->paginationEnabled());
        static::assertSame('date', $config->getOrderBy());
        static::assertSame(OrderDirection::DESC, $config->getOrder());
        static::assertNotEmpty($config->getTerms());
        static::assertNull($config->getDateFrom());
        static::assertNull($config->getDateTo());
    }

    #[TestDox('maps dateFrom and dateTo attributes to getPostsConfig')]
    public function testMapWithDateFromAndDateTo(): void
    {
        $wpService = new class implements GetTerms {
            public function getTerms(array|string $args = [], array|string $deprecated = ''): array|string|\WP_Error
            {
                return [];
            }
        };
        $mapper = new BlockAttributesToGetPostsConfigMapper($wpService);

        $attributes = static::getDefaultAttributes();
        $attributes['dateFrom'] = '2024-01-01';
        $attributes['dateTo'] = '2024-12-31';

        $config = $mapper->map($attributes);

        static::assertSame('2024-01-01', $config->getDateFrom());
        static::assertSame('2024-12-31', $config->getDateTo());
    }

    private static function getDefaultAttributes(): array
    {
        return [
            'dateFrom' => '',
            'dateTo' => '',
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
    }
}
