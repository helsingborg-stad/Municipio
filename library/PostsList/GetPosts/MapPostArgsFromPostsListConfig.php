<?php

namespace Municipio\PostsList\GetPosts;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use WpService\Contracts\GetPosts;

/**
 * Map to posts args from posts list config
 */
class MapPostArgsFromPostsListConfig
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
     * Get posts args
     *
     * @return array
     */
    public function getPosts(): array
    {
        $args     = $args ?? [];
        $appliers = [
            new PostsListConfigToGetPostsArgs\ApplyOrder(),
            new PostsListConfigToGetPostsArgs\ApplyPage(),
            new PostsListConfigToGetPostsArgs\ApplyPostsPerPage(),
            new PostsListConfigToGetPostsArgs\ApplyPostType(),
            new PostsListConfigToGetPostsArgs\ApplySearch(),
            new PostsListConfigToGetPostsArgs\ApplyTaxQuery(),
        ];

        foreach ($appliers as $applier) {
            $args = array_merge($args, $applier->apply($this->config, $args));
        }

        return $args;
    }
}
