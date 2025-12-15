<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply included post IDs from posts list config to get posts args
 */
class ApplyIncludedPostIds implements ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Apply included post IDs from posts list config to get posts args
     *
     * @param GetPostsConfigInterface $config
     * @param array $args
     * @return array
     */
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [
            ...$args,
            'post__in' => $config->getIncludedPostIds(),
        ];
    }
}
