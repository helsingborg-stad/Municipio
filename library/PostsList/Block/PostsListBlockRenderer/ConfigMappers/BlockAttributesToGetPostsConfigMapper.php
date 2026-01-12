<?php

declare(strict_types=1);

namespace Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use WpService\Contracts\GetTerms;

class BlockAttributesToGetPostsConfigMapper
{
    public function __construct(
        private GetTerms $wpService,
    ) {}

    public function map(array $attributes): GetPostsConfigInterface
    {
        //$attributes['terms'] = is_array($attributes['terms']) ? $attributes['terms'] : [];
        $terms = array_map(fn(array $term) => $this->wpService->getTerms([
            'taxonomy' => $term['taxonomy'],
            'include' => $term['terms'] ?? [],
        ]), $attributes['terms'] ?? []);

        // flatten terms array
        $terms = array_reduce($terms, \array_merge(...), []);
        $terms = array_filter($terms, static fn($item) => is_a($item, \WP_Term::class));
        $order = $attributes['order'] === 'desc' ? OrderDirection::DESC : OrderDirection::ASC;
        $postsPerPage = (int) ($attributes['postsPerPage'] ?? 12);

        return new class($attributes, $terms, $attributes['orderBy'], $order, $postsPerPage) extends DefaultGetPostsConfig {
            public function __construct(
                private array $attributes,
                private array $terms,
                private null|string $orderBy,
                private OrderDirection $order,
                private int $postsPerPage,
            ) {}

            public function getPostTypes(): array
            {
                return [$this->attributes['postType'] ?? 'post'];
            }

            public function paginationEnabled(): bool
            {
                return $this->attributes['paginationEnabled'] ?? true;
            }

            public function getPostsPerPage(): int
            {
                return $this->postsPerPage;
            }

            public function getTerms(): array
            {
                return $this->terms;
            }

            public function getOrder(): OrderDirection
            {
                return $this->order;
            }

            public function getOrderBy(): string
            {
                return $this->orderBy ?? 'date';
            }

            public function getDateSource(): string
            {
                return $this->attributes['dateSource'] ?? 'post_date';
            }

            public function getDateFrom(): null|string
            {
                return isset($this->attributes['dateFrom']) && $this->attributes['dateFrom'] !== '' ? $this->attributes['dateFrom'] : null;
            }

            public function getDateTo(): null|string
            {
                return isset($this->attributes['dateTo']) && $this->attributes['dateTo'] !== '' ? $this->attributes['dateTo'] : null;
            }
        };
    }
}
