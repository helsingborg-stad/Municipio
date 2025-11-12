<?php

namespace Municipio\PostsList\GetPosts;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

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
    ) {
    }

    /**
     * Get posts args
     *
     * @return array
     */
    public function getPostsArgs(): array
    {
        $args     = $args ?? [];
        $appliers = [
            new PostsListConfigToGetPostsArgs\ApplyDate(),
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
