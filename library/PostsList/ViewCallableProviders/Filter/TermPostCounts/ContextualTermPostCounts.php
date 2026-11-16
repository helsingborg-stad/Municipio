<?php

declare(strict_types=1);

namespace Municipio\PostsList\ViewCallableProviders\Filter\TermPostCounts;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\GetPosts\MapPostArgsFromPostsListConfig;
use Municipio\PostsList\GetPosts\WpQueryFactoryInterface;
use WpService\Contracts\WpCacheGet;
use WpService\Contracts\WpCacheGetLastChanged;
use WpService\Contracts\WpCacheSet;

/**
 * Counts posts per term among the posts the list would show.
 *
 * The counts use the same post types, date range (e.g. passed events filtered out), search and
 * included posts as the list itself. Selected terms narrow the counts as follows:
 * - Facetting enabled (AND between taxonomies): a taxonomy's counts are narrowed by the terms
 *   selected in the other taxonomies, but not by its own, so that choosing another term in the
 *   same taxonomy (OR within a taxonomy) shows how many posts it would add.
 * - Facetting disabled (OR between taxonomies): selecting a term only ever widens the list, so
 *   counts are not narrowed by selected terms.
 */
class ContextualTermPostCounts implements TermPostCountsInterface
{
    private const CACHE_GROUP  = 'municipio_posts_list_term_counts';
    private const CACHE_EXPIRE = 86400;

    /** @var array<string, array<int, int>> */
    private array $countsByTaxonomy;

    /**
     * Constructor
     *
     * @param GetPostsConfigInterface $getPostsConfig
     * @param FilterConfigInterface $filterConfig
     * @param AppearanceConfigInterface $appearanceConfig
     * @param WpQueryFactoryInterface $wpQueryFactory
     * @param \wpdb $wpdb
     * @param WpCacheGet&WpCacheSet&WpCacheGetLastChanged $wpService
     */
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private FilterConfigInterface $filterConfig,
        private AppearanceConfigInterface $appearanceConfig,
        private WpQueryFactoryInterface $wpQueryFactory,
        private \wpdb $wpdb,
        private WpCacheGet&WpCacheSet&WpCacheGetLastChanged $wpService,
    ) {}

    /**
     * @inheritDoc
     */
    public function getCounts(string $taxonomy): array
    {
        if (!isset($this->countsByTaxonomy)) {
            $this->countsByTaxonomy = $this->resolveCounts();
        }

        return $this->countsByTaxonomy[$taxonomy] ?? [];
    }

    /**
     * Resolve counts for all taxonomies enabled for filtering.
     *
     * Taxonomies that are narrowed by the same set of selected terms share one posts query.
     *
     * @return array<string, array<int, int>>
     */
    private function resolveCounts(): array
    {
        $taxonomiesByNarrowingTerms = [];
        foreach ($this->filterConfig->getTaxonomiesEnabledForFiltering() as $taxonomyFilterConfig) {
            $taxonomy       = $taxonomyFilterConfig->getTaxonomy()->name;
            $narrowingTerms = $this->getNarrowingTerms($taxonomy);
            $key            = implode(',', array_map(static fn($term) => $term->term_id, $narrowingTerms));

            $taxonomiesByNarrowingTerms[$key]['terms']        = $narrowingTerms;
            $taxonomiesByNarrowingTerms[$key]['taxonomies'][] = $taxonomy;
        }

        $countsByTaxonomy = [];
        foreach ($taxonomiesByNarrowingTerms as $group) {
            $countsByTaxonomy = [
                ...$countsByTaxonomy,
                ...$this->getCountsForTaxonomies($group['taxonomies'], $group['terms']),
            ];
        }

        return $countsByTaxonomy;
    }

    /**
     * Get the selected terms that should narrow the counts of a taxonomy.
     *
     * @param string $taxonomy
     * @return \WP_Term[]
     */
    private function getNarrowingTerms(string $taxonomy): array
    {
        if (!$this->getPostsConfig->isFacettingTaxonomyQueryEnabled()) {
            return [];
        }

        return array_values(array_filter(
            $this->getPostsConfig->getTerms(),
            static fn($term) => $term->taxonomy !== $taxonomy,
        ));
    }

    /**
     * Get counts for taxonomies among the posts matching the list, narrowed by the given terms.
     *
     * @param string[] $taxonomies
     * @param \WP_Term[] $narrowingTerms
     * @return array<string, array<int, int>>
     */
    private function getCountsForTaxonomies(array $taxonomies, array $narrowingTerms): array
    {
        $args     = $this->getPostIdsQueryArgs($narrowingTerms);
        $cacheKey = $this->getCacheKey($args, $taxonomies);
        $found    = false;
        $cached   = $this->wpService->wpCacheGet($cacheKey, self::CACHE_GROUP, false, $found);

        if ($found && is_array($cached)) {
            return $cached;
        }

        $postIds = array_map('intval', $this->wpQueryFactory::create($args)->posts);
        $counts  = array_fill_keys($taxonomies, []);

        foreach ($this->queryTermCounts($taxonomies, $postIds) as $row) {
            $counts[$row->taxonomy][(int) $row->term_id] = (int) $row->post_count;
        }

        $this->wpService->wpCacheSet($cacheKey, $counts, self::CACHE_GROUP, self::CACHE_EXPIRE);

        return $counts;
    }

    /**
     * Get args for a query returning the IDs of all posts the list would show.
     *
     * @param \WP_Term[] $narrowingTerms
     */
    private function getPostIdsQueryArgs(array $narrowingTerms): array
    {
        $config = new GetPostsConfigWithTerms($this->getPostsConfig, $narrowingTerms);
        $args   = (new MapPostArgsFromPostsListConfig($config, $this->filterConfig, $this->appearanceConfig))->getPostsArgs();

        unset($args['paged'], $args['order'], $args['meta_key']);

        return [
            ...$args,
            'fields'                 => 'ids',
            'posts_per_page'         => -1,
            'no_found_rows'          => true,
            'orderby'                => 'none',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ];
    }

    /**
     * Count posts per term for the given taxonomies among the given posts.
     *
     * @param string[] $taxonomies
     * @param int[] $postIds
     * @return object[] Rows with taxonomy, term_id and post_count.
     */
    private function queryTermCounts(array $taxonomies, array $postIds): array
    {
        if (empty($taxonomies) || empty($postIds)) {
            return [];
        }

        $taxonomyPlaceholders = implode(',', array_fill(0, count($taxonomies), '%s'));
        $postIdPlaceholders   = implode(',', array_fill(0, count($postIds), '%d'));

        $sql = $this->wpdb->prepare(
            "SELECT tt.taxonomy, tt.term_id, COUNT(DISTINCT tr.object_id) AS post_count
            FROM {$this->wpdb->term_relationships} tr
            INNER JOIN {$this->wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
            WHERE tt.taxonomy IN ({$taxonomyPlaceholders})
            AND tr.object_id IN ({$postIdPlaceholders})
            GROUP BY tt.taxonomy, tt.term_id",
            ...$taxonomies,
            ...$postIds,
        );

        return $this->wpdb->get_results($sql) ?: [];
    }

    /**
     * Get a cache key that changes whenever posts or terms change.
     *
     * @param array $args
     * @param string[] $taxonomies
     */
    private function getCacheKey(array $args, array $taxonomies): string
    {
        return md5(serialize([
            $args,
            $taxonomies,
            $this->wpService->wpCacheGetLastChanged('posts'),
            $this->wpService->wpCacheGetLastChanged('terms'),
        ]));
    }
}
