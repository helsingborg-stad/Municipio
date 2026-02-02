<?php

declare(strict_types=1);

namespace Municipio\PostsList\Block\PostsListBlockRenderer;

use ComponentLibrary\Renderer\RendererInterface;
use Municipio\PostsList\PostsListFactoryInterface;
use Municipio\PostsList\QueryVars\QueryVars;
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
        $prefix = $attributes['anchor'] ?? $attributes['queryVarsPrefix'] ?? 'posts_list_block_' . md5(json_encode($attributes));
        $prefix = rtrim($prefix, '_') . '_';
        $queryVars = new QueryVars($prefix);

        $postsListConfigDTO = (new \Municipio\PostsList\ConfigMapper\BlockAttributesToPostsListConfigMapper($this->wpService, $queryVars))->map([
            ...$attributes,
            'queryVarsPrefix' => $queryVars->getPrefix(),
        ]);
        $postsList = $this->postsListFactory->create($postsListConfigDTO);
        $data = $postsList->getData();
        $data['asyncAttributes'] = $attributes;

        return $this->renderer->render('posts-list', $data);
    }
}
