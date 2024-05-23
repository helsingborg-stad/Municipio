<?php

namespace Municipio\ExternalContent\PostsResults;

use Municipio\ExternalContent\PostsResults\Helpers\IsQueryForExternalContent;
use Municipio\HooksRegistrar\Hookable;
use WP_Query;
use WpService\Contracts\AddFilter;
use WpService\Contracts\CacheSet;

class AddExternalContentToWpCache implements Hookable, PostsResultsDecorator
{
    public function __construct(private AddFilter&CacheSet $wpService, private IsQueryForExternalContent $helpers)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('posts_results', [$this, 'apply'], 10, 2);
    }

    public function apply(array $posts, WP_Query $query): array
    {
        if (empty($posts) || !$this->helpers->isQueryForExternalContent($query)) {
            return $posts;
        }

        foreach ($posts as $post) {
            $this->wpService->cacheSet($post->ID, $post, 'posts');
        }

        return $posts;
    }
}
