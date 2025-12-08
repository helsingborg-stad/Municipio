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
        $postsListConfigDTO = (new \Municipio\PostsList\ConfigMapper\BlockAttributesToPostsListConfigMapper())->map([
            ...$attributes,
            'queryVarsPrefix' => 'posts_list_block_' . md5(json_encode($attributes)) . '_',
        ]);
        $postsList = $this->postsListFactory->create(
            $postsListConfigDTO->getPostsConfig,
            $postsListConfigDTO->appearanceConfig,
            $postsListConfigDTO->filterConfig,
            $postsListConfigDTO->queryVarsPrefix,
        );
        $data = $postsList->getData();
        return render_blade_view('posts-list', $data);
    }
}
