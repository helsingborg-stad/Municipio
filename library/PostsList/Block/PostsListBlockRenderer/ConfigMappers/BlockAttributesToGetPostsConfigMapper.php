<?php

namespace Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers;

use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;

class BlockAttributesToGetPostsConfigMapper
{
    public function map(array $attributes): GetPostsConfigInterface
    {
        $terms = array_map(function (array $term) {
            return get_terms([
                'taxonomy' => $term['taxonomy'],
                'include' => $term['terms'] ?? [],
            ]);
        }, $attributes['terms'] ?? []);

        $terms = array_filter($terms, fn($item) => is_a($item, \WP_Term::class));

        // flatten terms array
        $terms = array_reduce(
            $terms,
            function ($carry, $item) {
                return array_merge($carry, $item);
            },
            [],
        );

        $order = $attributes['order'] === 'desc' ? OrderDirection::DESC : OrderDirection::ASC;

        return new class($attributes, $terms, $attributes['orderBy'], $order) extends DefaultGetPostsConfig {
            public function __construct(
                private array $attributes,
                private array $terms,
                private null|string $orderBy,
                private OrderDirection $order,
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
                return $this->attributes['postsPerPage'];
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
        };
    }
}
