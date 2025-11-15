<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply post type from posts list config to get posts args
 */
class ApplyPostType implements ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Apply post type from posts list config to get posts args
     *
     * @param GetPostsConfigInterface $config
     * @param array $args
     * @return array
     */
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [...$args, 'post_type' => $config->getPostTypes()];
    }
}
