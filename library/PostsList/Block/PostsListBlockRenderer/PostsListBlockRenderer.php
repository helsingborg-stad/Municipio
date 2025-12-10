<?php

namespace Municipio\PostsList\Block\PostsListBlockRenderer;

use ComponentLibrary\Renderer\RendererInterface;
use Municipio\PostsList\PostsListFactoryInterface;

class PostsListBlockRenderer implements BlockRendererInterface
{
    public function __construct(
        private PostsListFactoryInterface $postsListFactory,
        private RendererInterface $renderer,
    ) {}

    public function render(array $attributes, string $content, \WP_Block $block): string
    {
        $postsListConfigDTO = (new \Municipio\PostsList\ConfigMapper\BlockAttributesToPostsListConfigMapper())->map([
            ...$attributes,
            'queryVarsPrefix' => 'posts_list_block_' . md5(json_encode($attributes)) . '_',
        ]);
        $postsList = $this->postsListFactory->create($postsListConfigDTO);
        $data = $postsList->getData();

        return $this->renderer->render('posts-list', $data);
    }
}
