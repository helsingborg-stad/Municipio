<?php

namespace Municipio\PostsList\Block;

use Municipio\PostsList\Config\AppearanceConfig\DateFormat;
use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;
use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use Municipio\PostsList\PostsListFactoryInterface;

class PostsListBlockRenderer implements BlockRendererInterface
{
    public function __construct(
        private PostsListFactoryInterface $postsListFactory,
    ) {}

    public function render(array $attributes, string $content, \WP_Block $block): string
    {
        global $wpService, $acfService;
        $appearanceConfig = new class($attributes) extends DefaultAppearanceConfig {
            public function __construct(
                private array $attributes,
            ) {}

            public function getNumberOfColumns(): int
            {
                return $this->attributes['numberOfColumns'] ?? 3;
            }

            public function getDesign(): PostDesign
            {
                return PostDesign::from($this->attributes['design'] ?? 'card');
            }

            public function getDateFormat(): DateFormat
            {
                return DateFormat::from($this->attributes['dateFormat']);
            }

            public function getDateSource(): string
            {
                return $this->attributes['dateSource'];
            }
        };

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

        $getPostsConfig = new class($attributes, $terms, $attributes['orderBy'], $order) extends DefaultGetPostsConfig {
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
        };

        $taxonomiesEnabledForFiltering = array_filter(
            $attributes['taxonomiesEnabledForFiltering'] ?? [],
            function ($item) {
                return is_array($item) && isset($item['taxonomy'], $item['type']);
            },
        );

        $taxonomyFilterConfigs = [];

        foreach ($taxonomiesEnabledForFiltering as $item) {
            try {
                if (!isset($GLOBALS['wp_taxonomies'][$item['taxonomy']])) {
                    continue;
                }
                $taxonomyFilterConfigs[] = new TaxonomyFilterConfig(
                    $GLOBALS['wp_taxonomies'][$item['taxonomy']],
                    TaxonomyFilterType::from($item['type']),
                );
            } catch (\Throwable $e) {
                // Ignore invalid taxonomy or type
            }
        }

        $filterConfig = new class($attributes, $taxonomyFilterConfigs) extends DefaultFilterConfig {
            public function __construct(
                private array $attributes,
                private array $taxonomyFilterConfigs,
            ) {}

            public function isTextSearchEnabled(): bool
            {
                return $this->attributes['textSearchEnabled'] ?? false;
            }

            public function isDateFilterEnabled(): bool
            {
                return $this->attributes['dateFilterEnabled'] ?? false;
            }

            public function getTaxonomiesEnabledForFiltering(): array
            {
                return $this->taxonomyFilterConfigs;
            }
        };

        $data = $this->postsListFactory
            ->create(
                $getPostsConfig,
                $appearanceConfig,
                $filterConfig,
                'posts_list_block_' . md5(json_encode($attributes)) . '_',
            )
            ->getData();

        return render_blade_view('posts-list', $data);
    }
}
