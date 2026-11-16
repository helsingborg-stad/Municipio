<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders\Filter\TermPostCounts;

use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\GetPosts\WpQueryFactoryInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WP_Taxonomy;
use WP_Term;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheGetLastChanged;
use WpService\Contracts\WpCacheSet;

class ContextualTermPostCountsTest extends TestCase
{
    #[TestDox('sets counts from the posts in the list and leaves out unselected terms matching no posts')]
    public function testAppliesCountsToTerms(): void
    {
        [$counts] = $this->createSut(rows: [
            (object) ['taxonomy' => 'category', 'term_id' => '10', 'post_count' => '2'],
            (object) ['taxonomy' => 'audience', 'term_id' => '20', 'post_count' => '1'],
        ]);
        $terms    = [$this->createTerm(10, 'category'), $this->createTerm(11, 'category'), $this->createTerm(12, 'category')];

        $result = $counts->applyToTerms('category', $terms, ['term-12']);

        static::assertSame([10 => 2, 12 => 0], array_column(array_map(static fn($term) => (array) $term, $result), 'count', 'term_id'));
        static::assertSame(5, $terms[0]->count, 'The given terms must not be modified.');
    }

    #[TestDox('queries all post IDs once, without pagination, using the date range of the list')]
    public function testQueriesAllPostIdsOnceUsingListDateRange(): void
    {
        [$counts, $factory] = $this->createSut($this->createGetPostsConfig(dateFrom: '2026-11-16', dateSource: 'startDate'));

        $counts->applyToTerms('category', [], []);
        $counts->applyToTerms('audience', [], []);

        static::assertCount(1, $factory::$createdWithArgs);
        $args = $factory::$createdWithArgs[0];
        static::assertSame('ids', $args['fields']);
        static::assertSame(-1, $args['posts_per_page']);
        static::assertArrayNotHasKey('paged', $args);
        static::assertArrayNotHasKey('tax_query', $args);
        static::assertSame('startDate', $args['meta_query']['date_clause']['key']);
        static::assertSame('2026-11-16 00:00:00', $args['meta_query']['date_clause']['value']);
    }

    #[TestDox('narrows the counts of a taxonomy by the terms selected in other taxonomies when facetting')]
    public function testNarrowsByOtherTaxonomiesWhenFacetting(): void
    {
        $selected           = [$this->createTerm(10, 'category'), $this->createTerm(20, 'audience')];
        [$counts, $factory] = $this->createSut($this->createGetPostsConfig(terms: $selected, facetting: true));

        $counts->applyToTerms('category', [], []);

        $narrowedBy = array_map(
            static fn(array $args) => array_map(
                static fn(array $clause) => [$clause['taxonomy'], $clause['terms']],
                array_values(array_filter($args['tax_query'], 'is_array')),
            ),
            $factory::$createdWithArgs,
        );
        static::assertSame([[['audience', [20]]], [['category', [10]]]], $narrowedBy);
    }

    #[TestDox('does not narrow counts by selected terms when not facetting')]
    public function testDoesNotNarrowWhenNotFacetting(): void
    {
        $selected           = [$this->createTerm(10, 'category'), $this->createTerm(20, 'audience')];
        [$counts, $factory] = $this->createSut($this->createGetPostsConfig(terms: $selected, facetting: false));

        $counts->applyToTerms('category', [], []);

        static::assertCount(1, $factory::$createdWithArgs);
        static::assertArrayNotHasKey('tax_query', $factory::$createdWithArgs[0]);
    }

    #[TestDox('does not query term counts when no posts match')]
    public function testSkipsTermQueryWhenNoPosts(): void
    {
        [$counts, , $wpdb] = $this->createSut(rows: [(object) ['taxonomy' => 'category', 'term_id' => '10', 'post_count' => '2']], postIds: []);

        static::assertSame([], $counts->applyToTerms('category', [$this->createTerm(10, 'category')], []));
        static::assertSame(0, $wpdb->getResultsCalls);
    }

    #[TestDox('uses cached counts when available')]
    public function testUsesCachedCounts(): void
    {
        [$counts, $factory] = $this->createSut(cached: ['category' => [10 => 7], 'audience' => []]);

        $result = $counts->applyToTerms('category', [$this->createTerm(10, 'category')], []);

        static::assertSame(7, $result[0]->count);
        static::assertCount(0, $factory::$createdWithArgs);
    }

    /**
     * @return array{0: ContextualTermPostCounts, 1: WpQueryFactoryInterface, 2: \wpdb}
     */
    private function createSut(
        ?GetPostsConfigInterface $config = null,
        array $rows = [],
        array $postIds = [1, 2, 3],
        ?array $cached = null,
    ): array {
        $factory = new class implements WpQueryFactoryInterface {
            public static array $createdWithArgs = [];
            public static array $postIds         = [];

            public static function create($query = ''): \WP_Query
            {
                self::$createdWithArgs[] = $query;
                $wpQuery                 = new class extends \WP_Query {
                    public function get_posts() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
                    {
                        return [];
                    }
                };
                $wpQuery->posts          = self::$postIds;
                return $wpQuery;
            }
        };
        $factory::$createdWithArgs = [];
        $factory::$postIds         = $postIds;

        $wpdb = new class ('', '', '', '') extends \wpdb {
            public array $rows          = [];
            public int $getResultsCalls = 0;

            public function prepare($query, ...$args)
            {
                return $query;
            }

            public function get_results($query = null, $output = OBJECT) // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
            {
                $this->getResultsCalls++;
                return $this->rows;
            }
        };
        $wpdb->rows               = $rows;
        $wpdb->term_relationships = 'wp_term_relationships';
        $wpdb->term_taxonomy      = 'wp_term_taxonomy';

        $taxonomyConfigs = array_map(static function (string $name) {
            $taxonomy       = new WP_Taxonomy($name, 'event');
            $taxonomy->name = $name;
            return new TaxonomyFilterConfig($taxonomy, TaxonomyFilterType::MULTISELECT);
        }, ['category', 'audience']);

        $filterConfig = new class ($taxonomyConfigs) extends DefaultFilterConfig {
            public function __construct(private array $taxonomyConfigs) {}

            public function getTaxonomiesEnabledForFiltering(): array
            {
                return $this->taxonomyConfigs;
            }
        };

        $wpService = new class ($cached) implements WpCacheGet, WpCacheSet, WpCacheGetLastChanged {
            public function __construct(private ?array $cached) {}

            public function wpCacheGet(int|string $key, string $group = '', bool $force = false, bool &$found = null): mixed
            {
                $found = $this->cached !== null;
                return $this->cached ?? false;
            }

            public function wpCacheSet(int|string $key, mixed $data, string $group = '', int $expire = 0): bool
            {
                return true;
            }

            public function wpCacheGetLastChanged(string $group): string
            {
                return '1';
            }
        };

        $counts = new ContextualTermPostCounts(
            $config ?? $this->createGetPostsConfig(),
            $filterConfig,
            new DefaultAppearanceConfig(),
            $factory,
            $wpdb,
            $wpService,
        );

        return [$counts, $factory, $wpdb];
    }

    private function createGetPostsConfig(
        array $terms = [],
        bool $facetting = false,
        ?string $dateFrom = null,
        string $dateSource = 'post_date',
    ): GetPostsConfigInterface {
        return new class ($terms, $facetting, $dateFrom, $dateSource) extends DefaultGetPostsConfig {
            public function __construct(
                private array $terms,
                private bool $facetting,
                private ?string $dateFrom,
                private string $dateSource,
            ) {}

            public function getTerms(): array
            {
                return $this->terms;
            }

            public function isFacettingTaxonomyQueryEnabled(): bool
            {
                return $this->facetting;
            }

            public function getDateFrom(): null|string
            {
                return $this->dateFrom;
            }

            public function getDateSource(): string
            {
                return $this->dateSource;
            }

            public function getPostTypes(): array
            {
                return ['event'];
            }
        };
    }

    private function createTerm(int $id, string $taxonomy): WP_Term
    {
        $term           = new WP_Term([]);
        $term->term_id  = $id;
        $term->taxonomy = $taxonomy;
        $term->slug     = "term-{$id}";
        $term->count    = 5;
        return $term;
    }
}
