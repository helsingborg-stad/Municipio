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
        $response = $this->provider->search($searchTerm);
        $hits = is_array($response) ? ($response['hits'] ?? []) : [];
        $postIds = array_values(array_filter(array_map(
            static fn(mixed $hit): int => is_array($hit) ? (int) ($hit['ID'] ?? 0) : 0,
            $hits,
        )));

        $query->set('post__in', $postIds !== [] ? $postIds : [PHP_INT_MAX]);
        $query->set('s', false);
        $query->set('orderby', 'post__in');
    }
}