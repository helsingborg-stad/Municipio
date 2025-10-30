<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

class ApplyPostsPerPage implements ApplyPostsListConfigToGetPostsArgsInterface
{
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [...$args, 'posts_per_page' => $config->getPostsPerPage()];
    }
}
