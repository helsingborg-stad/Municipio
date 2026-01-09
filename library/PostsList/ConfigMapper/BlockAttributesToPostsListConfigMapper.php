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

    public function map(mixed $sourceData): PostsListConfigDTOInterface
    {
        // $sourceData is expected to be the block attributes array
        $attributes = $sourceData;
        $prefix = ($attributes['anchor'] ?? 'posts_list_block_' . md5(json_encode($attributes))) . '_';

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
