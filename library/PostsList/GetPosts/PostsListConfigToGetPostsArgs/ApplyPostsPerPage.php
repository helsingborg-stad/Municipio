<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\PostsListConfigInterface;

class ApplyPostsPerPage implements ApplyPostsListConfigToGetPostsArgsInterface
{
    public function apply(PostsListConfigInterface $config, array $args): array
    {
        return [...$args, 'posts_per_page' => $config->getPostsPerPage()];
    }
}
