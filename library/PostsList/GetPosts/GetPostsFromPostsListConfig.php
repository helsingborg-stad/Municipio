<?php

namespace Municipio\PostsList\GetPosts;

use Municipio\Helper\Post;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use WpService\Contracts\GetPosts;

/**
 * Get posts from posts list config
 */
class GetPostsFromPostsListConfig
{
    /**
     * Constructor
     *
     * @param GetPostsConfigInterface $config
     * @param GetPosts $innerGetPosts
     */
    public function __construct(
        private GetPostsConfigInterface $config,
        private GetPosts $innerGetPosts
    ) {
    }

    /**
     * Get posts
     *
     * @return PostObjectInterface[]
     */
    public function getPosts(): array
    {
        $args     = $args ?? [];
        $appliers = [
            new PostsListConfigToGetPostsArgs\ApplyPostType(),
            new PostsListConfigToGetPostsArgs\ApplyPostsPerPage(),
            new PostsListConfigToGetPostsArgs\ApplySearch(),
            new PostsListConfigToGetPostsArgs\ApplyTaxQuery(),
        ];

        foreach ($appliers as $applier) {
            $args = array_merge($args, $applier->apply($this->config, $args));
        }

        return array_map(fn($wpPost) => Post::convertWpPostToPostObject($wpPost), $this->innerGetPosts->getPosts($args));
    }
}
