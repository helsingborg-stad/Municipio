<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Search;

use Municipio\SearchIndex\Provider\SearchProviderInterface;
use WpService\WpService;

/**
 * Replaces the main search query with provider search results.
 */
class SearchQuery
{
    public function __construct(
        private WpService $wpService,
        private SearchProviderInterface $provider,
    ) {}

    /**
     * Register the query hook.
     */
    public function addHooks(): void
    {
        $this->wpService->addAction('pre_get_posts', [$this, 'applySearchResults']);
        $this->wpService->addFilter('posts_search', [$this, 'disableNativeSearch'], 10, 2);
    }

    /**
     * Apply matching provider record IDs to the main front-end search query.
     */
    public function applySearchResults(\WP_Query $query): void
    {
        if ($this->wpService->isAdmin() || !$query->is_main_query() || !$query->is_search()) {
            return;
        }

        if (!(bool) $this->wpService->applyFilters('Municipio/SearchIndex/BackendSearchActive', true, $query)) {
            $query->set('post__in', [PHP_INT_MAX]);
            $query->set('posts_per_page', 1);
            return;
        }

        $searchTerm = (string) $query->get('s');
        $page = max(1, (int) $query->get('paged'));
        $pageSize = max(1, (int) $query->get('posts_per_page'));

        try {
            $response = $this->provider->search($searchTerm, $page, $pageSize);
        } catch (\Throwable) {
            return;
        }

        $hits = is_array($response) ? ($response['hits'] ?? []) : [];
        if ($this->wpService->isMultisite()) {
            $blogId = $this->wpService->getCurrentBlogId();
            $hits = array_filter($hits, static fn(mixed $hit): bool => is_array($hit) && isset($hit['blog_id']) && (int) $hit['blog_id'] === $blogId);
        }
        $postIds = array_values(array_filter(array_map(
            static fn(mixed $hit): int => is_array($hit) ? (int) ($hit['ID'] ?? 0) : 0,
            $hits,
        )));

        $query->set('post__in', $postIds !== [] ? $postIds : [PHP_INT_MAX]);
        $query->set('_municipio_search_index_provider_query', true);
        $query->set('orderby', 'post__in');
        if (is_array($response) && isset($response['nbHits'])) {
            $query->found_posts = (int) $response['nbHits'];
            $query->max_num_pages = (int) ceil($query->found_posts / $pageSize);
        } elseif (is_array($response) && isset($response['found'])) {
            $query->found_posts = (int) $response['found'];
            $query->max_num_pages = (int) ceil($query->found_posts / $pageSize);
        }
    }

    /**
     * Prevent WordPress from applying its native search SQL to provider IDs.
     */
    public function disableNativeSearch(string $search, \WP_Query $query): string
    {
        return $query->get('_municipio_search_index_provider_query') ? '' : $search;
    }
}