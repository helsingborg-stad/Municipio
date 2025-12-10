<?php

declare(strict_types=1);

namespace Municipio\PostsList\ConfigMapper;

/**
 * Maps block attributes to PostsList config objects
 */
class BlockAttributesToPostsListConfigMapper implements PostsListConfigMapperInterface
{
    public function map(mixed $sourceData): PostsListConfigDTOInterface
    {
        // $sourceData is expected to be the block attributes array
        $attributes = $sourceData;
        $prefix = $attributes['queryVarsPrefix'] ?? 'posts_list_block_' . md5(json_encode($attributes)) . '_';

        $getPostsConfig = (new \Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers\BlockAttributesToGetPostsConfigMapper())->map(
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
