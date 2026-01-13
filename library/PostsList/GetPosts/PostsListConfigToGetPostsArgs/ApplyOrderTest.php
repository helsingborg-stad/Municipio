<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ApplyOrderTest extends TestCase
{
    #[TestDox('apply adds order and order from config to args')]
    public function testApplyOrder(): void
    {
        $configWithDescOrder = new class extends DefaultGetPostsConfig {
            public function getOrder(): OrderDirection
            {
                return OrderDirection::DESC;
            }
        };

        $configWithAscOrder = new class extends DefaultGetPostsConfig {
            public function getOrder(): OrderDirection
            {
                return OrderDirection::ASC;
            }
        };

        $applier = new ApplyOrder(new DefaultAppearanceConfig());

        $argsWithDescOrder = $applier->apply($configWithDescOrder, []);
        $argsWithAscOrder = $applier->apply($configWithAscOrder, []);

        $this->assertEquals('DESC', $argsWithDescOrder['order']);
        $this->assertEquals('ASC', $argsWithAscOrder['order']);
    }

    #[TestDox('applies orderby for post table fields if instructed to')]
    #[DataProvider('provideConfigsForPostTableFields')]
    public function testApplyOrderForPostTableFields(string $orderBy): void
    {
        $config = new class($orderBy) extends DefaultGetPostsConfig {
            public function __construct(
                private string $orderBy,
            ) {}

            public function getOrderBy(): string
            {
                return $this->orderBy;
            }
        };

        $applier = new ApplyOrder(new DefaultAppearanceConfig());

        $args = $applier->apply($config, []);

        $this->assertEquals($orderBy, $args['orderby']);
    }

    #[TestDox('applies orderby for custom fields if no match with post table fields')]
    public function testApplyOrderForCustomFields(): void
    {
        $customFieldKey = 'my_custom_field';
        $config = new class($customFieldKey) extends DefaultGetPostsConfig {
            public function __construct(
                private string $orderBy,
            ) {}

            public function getOrderBy(): string
            {
                return $this->orderBy;
            }
        };

        $applier = new ApplyOrder(new DefaultAppearanceConfig());
        $args = $applier->apply($config, []);

        $this->assertEquals('meta_value', $args['orderby']);
        $this->assertEquals($customFieldKey, $args['meta_key']);
    }

    #[TestDox('normalizes post table field names')]
    #[DataProvider('provideNormalizesPostTableFieldNames')]
    public function testNormalizesPostTableFieldNames(string $input, string $expected): void
    {
        $config = new class($input) extends DefaultGetPostsConfig {
            public function __construct(
                private string $orderBy,
            ) {}

            public function getOrderBy(): string
            {
                return $this->orderBy;
            }
        };

        $applier = new ApplyOrder(new DefaultAppearanceConfig());
        $args = $applier->apply($config, []);

        $this->assertEquals($expected, $args['orderby']);
    }

    /**
     * Ensures that when ordering by a custom field that is also used as the date source
     * in the appearance config, and when a date filter (date from or date to) is set in the get posts config,
     * the named meta query created for the date filter is reused for ordering.
     * This prevents issues when ordering by custom field that contains multiple entries and possibly dates that are not valid, given the date filter.
     */
    #[TestDox('uses named meta query when ordering by same custom field used as date source in appearance config and date from or date to is set in filter config')]
    public function testUsesNamedMetaQueryFromDateQuery(): void
    {
        $customFieldKey = 'my_date_field';

        $appearanceConfig = new class($customFieldKey) extends DefaultAppearanceConfig {
            public function __construct(
                private string $dateSource,
            ) {}

            public function getDateSource(): string
            {
                return $this->dateSource;
            }
        };

        $getPostsConfig = new class($customFieldKey) extends DefaultGetPostsConfig {
            public function __construct(
                private string $orderBy,
            ) {}

            public function getOrderBy(): string
            {
                return $this->orderBy;
            }

            public function getDateFrom(): null|string
            {
                return '2023-01-01';
            }

            public function getOrder(): OrderDirection
            {
                return OrderDirection::ASC;
            }
        };

        $applier = new ApplyOrder($appearanceConfig);
        $args = $applier->apply($getPostsConfig, []);

        $this->assertEquals(
            [
                'orderby' => [
                    ApplyDate::META_QUERY_KEY => $getPostsConfig->getOrder()->value,
                ],
            ],
            $args,
        );
    }

    public static function provideConfigsForPostTableFields(): array
    {
        return [
            'title' => ['title'],
            'date' => ['date'],
            'modified' => ['modified'],
        ];
    }

    public static function provideNormalizesPostTableFieldNames(): array
    {
        return [
            'post_title' => ['post_title', 'title'],
            'post_date' => ['post_date', 'date'],
            'post_modified' => ['post_modified', 'modified'],
        ];
    }
}
