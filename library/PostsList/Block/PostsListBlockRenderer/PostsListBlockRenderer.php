<?php

declare(strict_types=1);

namespace Municipio\PostsList\Block\PostsListBlockRenderer;

use ComponentLibrary\Renderer\RendererInterface;
use Municipio\PostsList\PostsListFactoryInterface;
use WpService\Contracts\GetTerms;

class PostsListBlockRenderer implements BlockRendererInterface
{
    public function __construct(
        private PostsListFactoryInterface $postsListFactory,
        private RendererInterface $renderer,
        private GetTerms $wpService,
    ) {}

    public function render(array $attributes, string $content, \WP_Block $block): string
    {
        $queryVarsPrefix = $attributes['anchor'] ?? $attributes['queryVarsPrefix'] ?? 'posts_list_block_' . md5(json_encode($attributes));
        $queryVarsPrefix = rtrim($queryVarsPrefix, '_') . '_';

        $postsListConfigDTO = (new \Municipio\PostsList\ConfigMapper\BlockAttributesToPostsListConfigMapper($this->wpService))->map([
            ...$attributes,
            'queryVarsPrefix' => $queryVarsPrefix,
        ]);
        $postsList = $this->postsListFactory->create($postsListConfigDTO);
        $data = $postsList->getData();

        return $this->renderer->render('posts-list', $data);
    }
}
