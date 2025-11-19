<?php

namespace Municipio\PostsList\Block;

use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;
use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\GetPosts\WpQueryFactory;
use Municipio\PostsList\PostsList;
use Municipio\PostsList\PostsListFactoryInterface;
use Municipio\PostsList\QueryVars\QueryVars;

class PostsListBlockRenderer implements BlockRendererInterface
{
    public function __construct(private PostsListFactoryInterface $postsListFactory)
    {
    }

    public function render(array $attributes, string $content, \WP_Block $block): string
    {
        global $wpService, $acfService;
        $appearanceConfig = new class ($attributes) extends DefaultAppearanceConfig {
            public function __construct(private array $attributes)
            {
            }
            public function getNumberOfColumns(): int
            {
                return $this->attributes['numberOfColumns'] ?? 3;
            }
            public function getDesign(): PostDesign
            {
                return PostDesign::from($this->attributes['design'] ?? 'card');
            }
        };

        $getPostsConfig = new class ($attributes) extends DefaultGetPostsConfig {
            public function __construct(private array $attributes)
            {
            }

            public function getPostTypes(): array
            {
                return [$this->attributes['postType'] ?? 'post'];
            }

            public function getPostsPerPage(): int
            {
                return $this->attributes['postsPerPage'];
            }
        };

        $taxonomyFilterConfigs = array_map(function ($taxonomy) {
            return new TaxonomyFilterConfig($taxonomy, TaxonomyFilterType::MULTISELECT);
        }, array_filter($GLOBALS['wp_taxonomies'], function ($taxonomy) use ($attributes) {
            return in_array($taxonomy->name, $attributes['taxonomiesEnabledForFiltering']);
        }));

        $filterConfig = new class ($attributes, $taxonomyFilterConfigs) extends DefaultFilterConfig {
            public function __construct(private array $attributes, private array $taxonomyFilterConfigs)
            {
            }

            public function isEnabled(): bool
            {
                return $this->attributes['enableFilters'] ?? false;
            }

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

        $data = $this->postsListFactory->create(
            $getPostsConfig,
            $appearanceConfig,
            $filterConfig,
            'posts_list_block_' . md5(json_encode($attributes)) . '_'
        )->getData();

        return render_blade_view('posts-list', $data);
    }
}
