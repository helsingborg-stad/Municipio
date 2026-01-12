<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

use WpService\Contracts\GetTerms;

/**
 * Maps block attributes to PostsList config objects
 */
class BlockAttributesToPostsListConfigMapper implements PostsListConfigMapperInterface
{
    public function __construct(
        private GetTerms $wpService,
    ) {}

    public function map(mixed $attributes): PostsListConfigDTOInterface
    {
        $prefix = $attributes['anchor'] ?? $attributes['queryVarsPrefix'] ?? 'posts_list_block_' . md5(json_encode($attributes));
        $prefix = rtrim($prefix, '_') . '_';

        $getPostsConfig = (new \Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers\BlockAttributesToGetPostsConfigMapper($this->wpService))->map(
            $attributes,
        );
        $appearanceConfig = (new \Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers\BlockAttributesToAppearanceConfigMapper())->map(
            $attributes,
        );
        $filterConfig = (new \Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers\BlockAttributesToFilterConfigMapper())->map(
            $attributes,
        );

        return new PostsListConfigDTO($getPostsConfig, $appearanceConfig, $filterConfig, $prefix);
    }
}
