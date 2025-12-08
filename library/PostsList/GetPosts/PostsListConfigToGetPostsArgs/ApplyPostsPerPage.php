<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

/*
 * Apply posts per page from posts list config to get posts args
 */
class ApplyPostsPerPage implements ApplyPostsListConfigToGetPostsArgsInterface
{
    /**
     * Apply posts per page from posts list config to get posts args
     *
     * @param GetPostsConfigInterface $config
     * @param array $args
     * @return array
     */
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [...$args, 'posts_per_page' => $config->getPostsPerPage()];
    }
}
