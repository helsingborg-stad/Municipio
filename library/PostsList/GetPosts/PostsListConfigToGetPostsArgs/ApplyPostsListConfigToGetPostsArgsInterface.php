<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

interface ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Applies mapping from PostsListConfig to GetPosts args array.
     *
     * @param GetPostsConfigInterface $config
     * @param array $args Current args array to apply mapping to.
     */
    public function apply(GetPostsConfigInterface $config, array $args): array;
}
