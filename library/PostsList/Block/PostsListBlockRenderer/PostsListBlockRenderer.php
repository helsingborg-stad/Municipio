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
        $data['asyncAttributes'] = $this->filterJsonSafeAttributes($attributes);

        return $this->renderer->render('posts-list', $data);
    }

    /**
     * Filter attributes to only include JSON-serializable values.
     */
    private function filterJsonSafeAttributes(array $attributes): array
    {
        $safe = [];
        foreach ($attributes as $key => $value) {
            if (is_scalar($value) || is_null($value)) {
                $safe[$key] = $value;
            } elseif (is_array($value)) {
                $safe[$key] = $this->filterJsonSafeArray($value);
            }
        }
        return $safe;
    }

    private function filterJsonSafeArray(array $array): array
    {
        $safe = [];
        foreach ($array as $key => $value) {
            if (is_scalar($value) || is_null($value)) {
                $safe[$key] = $value;
            } elseif (is_array($value)) {
                $safe[$key] = $this->filterJsonSafeArray($value);
            }
        }
        return $safe;
    }
}
