<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\PostsListConfigInterface;

interface ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Applies mapping from PostsListConfig to GetPosts args array.
     *
     * @param PostsListConfigInterface $config
     * @param array $args Current args array to apply mapping to.
     */
    public function apply(PostsListConfigInterface $config, array $args): array;
}
