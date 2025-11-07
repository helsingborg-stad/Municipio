<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply search from posts list config to get posts args
 */
class ApplySearch implements ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Apply search from posts list config to get posts args
     *
     * @param GetPostsConfigInterface $config
     * @param array $args
     * @return array
     */
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [...$args, 's' => $config->getSearch()];
    }
}
