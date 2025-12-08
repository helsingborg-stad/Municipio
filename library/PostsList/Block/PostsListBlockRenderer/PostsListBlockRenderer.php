<?php

namespace Municipio\PostsList\Block\PostsListBlockRenderer;

use Municipio\PostsList\PostsListFactoryInterface;

class PostsListBlockRenderer implements BlockRendererInterface
{
    public function __construct(
        private PostsListFactoryInterface $postsListFactory,
    ) {}

    public function render(array $attributes, string $content, \WP_Block $block): string
    {
        $appearanceConfig = (new ConfigMappers\BlockAttributesToAppearanceConfigMapper())->map($attributes);
        $getPostsConfig = (new ConfigMappers\BlockAttributesToGetPostsConfigMapper())->map($attributes);
        $filterConfig = (new ConfigMappers\BlockAttributesToFilterConfigMapper())->map($attributes);
        $prefix = 'posts_list_block_' . md5(json_encode($attributes)) . '_';

        $data = $this->postsListFactory->create($getPostsConfig, $appearanceConfig, $filterConfig, $prefix)->getData();

        return render_blade_view('posts-list', $data);
    }
}
