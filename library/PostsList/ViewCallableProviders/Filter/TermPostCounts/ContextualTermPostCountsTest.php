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
    protected function setUp(): void
    {
        FakeWpQueryFactory::$createdWithArgs = [];
        FakeWpQueryFactory::$postIds         = [1, 2, 3];
    }

    #[TestDox('returns post counts per term from the posts matching the list')]
    public function testReturnsCountsPerTerm(): void
    {
        $wpdb = $this->createWpdb([
            (object) ['taxonomy' => 'category', 'term_id' => '10', 'post_count' => '2'],
            (object) ['taxonomy' => 'audience', 'term_id' => '20', 'post_count' => '1'],
        ]);

        $counts = $this->createSut($this->createGetPostsConfig(), $wpdb);

        $this->assertSame([10 => 2], $counts->getCounts('category'));
        $this->assertSame([20 => 1], $counts->getCounts('audience'));
        $this->assertSame([], $counts->getCounts('unknown'));
    }

    #[TestDox('queries all post IDs without pagination, using the date range of the list')]
    public function testQueriesAllPostIdsUsingListDateRange(): void
    {
        $config = $this->createGetPostsConfig(dateFrom: '2026-11-16', dateSource: 'startDate');

        $this->createSut($config, $this->createWpdb([]))->getCounts('category');

        $args = FakeWpQueryFactory::$createdWithArgs[0];
        $this->assertSame('ids', $args['fields']);
        $this->assertSame(-1, $args['posts_per_page']);
        $this->assertArrayNotHasKey('paged', $args);
        $this->assertSame('startDate', $args['meta_query']['date_clause']['key']);
        $this->assertSame('2026-11-16 00:00:00', $args['meta_query']['date_clause']['value']);
    }

    #[TestDox('shares one posts query between taxonomies when no terms are selected')]
    public function testSharesQueryWhenNoTermsSelected(): void
    {
        $counts = $this->createSut($this->createGetPostsConfig(), $this->createWpdb([]));

        $counts->getCounts('category');
        $counts->getCounts('audience');

        $this->assertCount(1, FakeWpQueryFactory::$createdWithArgs);
        $this->assertArrayNotHasKey('tax_query', FakeWpQueryFactory::$createdWithArgs[0]);
    }

    #[TestDox('narrows the counts of a taxonomy by the terms selected in other taxonomies when facetting')]
    public function testNarrowsByOtherTaxonomiesWhenFacetting(): void
    {
        $config = $this->createGetPostsConfig(
            terms: [$this->createTerm(10, 'category'), $this->createTerm(20, 'audience')],
            facetting: true,
        );

        $this->createSut($config, $this->createWpdb([]))->getCounts('category');

        $this->assertCount(2, FakeWpQueryFactory::$createdWithArgs);
        $taxQueriesByTaxonomy = [];
        foreach (FakeWpQueryFactory::$createdWithArgs as $args) {
            $taxQueriesByTaxonomy[] = array_values(array_filter($args['tax_query'], 'is_array'));
        }

        // Counts for category are narrowed by audience only, and vice versa.
        $this->assertSame('audience', $taxQueriesByTaxonomy[0][0]['taxonomy']);
        $this->assertSame([20], $taxQueriesByTaxonomy[0][0]['terms']);
        $this->assertCount(1, $taxQueriesByTaxonomy[0]);
        $this->assertSame('category', $taxQueriesByTaxonomy[1][0]['taxonomy']);
        $this->assertSame([10], $taxQueriesByTaxonomy[1][0]['terms']);
        $this->assertCount(1, $taxQueriesByTaxonomy[1]);
    }

    #[TestDox('does not narrow counts by selected terms when not facetting')]
    public function testDoesNotNarrowWhenNotFacetting(): void
    {
        $config = $this->createGetPostsConfig(
            terms: [$this->createTerm(10, 'category'), $this->createTerm(20, 'audience')],
            facetting: false,
        );

        $this->createSut($config, $this->createWpdb([]))->getCounts('category');

        $this->assertCount(1, FakeWpQueryFactory::$createdWithArgs);
        $this->assertArrayNotHasKey('tax_query', FakeWpQueryFactory::$createdWithArgs[0]);
    }

    #[TestDox('does not query term counts when no posts match')]
    public function testSkipsTermQueryWhenNoPosts(): void
    {
        FakeWpQueryFactory::$postIds = [];
        $wpdb                        = $this->createWpdb([(object) ['taxonomy' => 'category', 'term_id' => '10', 'post_count' => '2']]);

        $this->assertSame([], $this->createSut($this->createGetPostsConfig(), $wpdb)->getCounts('category'));
        $this->assertSame(0, $wpdb->getResultsCalls);
    }

    #[TestDox('uses cached counts when available')]
    public function testUsesCachedCounts(): void
    {
        $wpService = $this->createWpService(['category' => [10 => 5], 'audience' => []]);
        $counts    = new ContextualTermPostCounts(
            $this->createGetPostsConfig(),
            $this->createFilterConfig(),
            new DefaultAppearanceConfig(),
            new FakeWpQueryFactory(),
            $this->createWpdb([]),
            $wpService,
        );

        $this->assertSame([10 => 5], $counts->getCounts('category'));
        $this->assertCount(0, FakeWpQueryFactory::$createdWithArgs);
    }

    private function createSut(GetPostsConfigInterface $config, \wpdb $wpdb): ContextualTermPostCounts
    {
        return new ContextualTermPostCounts(
            $config,
            $this->createFilterConfig(),
            new DefaultAppearanceConfig(),
            new FakeWpQueryFactory(),
            $wpdb,
            $this->createWpService(),
        );
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

    private function createFilterConfig(): DefaultFilterConfig
    {
        $taxonomies = [
            new TaxonomyFilterConfig($this->createTaxonomy('category'), TaxonomyFilterType::MULTISELECT),
            new TaxonomyFilterConfig($this->createTaxonomy('audience'), TaxonomyFilterType::MULTISELECT),
        ];

        return new class ($taxonomies) extends DefaultFilterConfig {
            public function __construct(private array $taxonomies) {}

            public function getTaxonomiesEnabledForFiltering(): array
            {
                return $this->taxonomies;
            }
        };
    }

    private function createTaxonomy(string $name): WP_Taxonomy
    {
        $taxonomy       = new WP_Taxonomy($name, 'event');
        $taxonomy->name = $name;
        return $taxonomy;
    }

    private function createTerm(int $id, string $taxonomy): WP_Term
    {
        $term           = new WP_Term([]);
        $term->term_id  = $id;
        $term->taxonomy = $taxonomy;
        $term->slug     = "term-$id";
        return $term;
    }

    private function createWpdb(array $rows): \wpdb
    {
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

        return $wpdb;
    }

    private function createWpService(mixed $cached = null): WpCacheGet&WpCacheSet&WpCacheGetLastChanged
    {
        return new class ($cached) implements WpCacheGet, WpCacheSet, WpCacheGetLastChanged {
            public function __construct(private mixed $cached) {}

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
    }
}

class FakeWpQueryFactory implements WpQueryFactoryInterface
{
    public static array $createdWithArgs = [];
    public static array $postIds         = [];

    public static function create($query = ''): \WP_Query
    {
        self::$createdWithArgs[] = $query;

        $wpQuery = new class extends \WP_Query {
            public function get_posts() // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
            {
                return [];
            }
        };

        $wpQuery->posts = self::$postIds;

        return $wpQuery;
    }
}
