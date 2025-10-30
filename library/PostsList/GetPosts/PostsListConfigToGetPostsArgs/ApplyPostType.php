<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

class ApplyPostType implements ApplyPostsListConfigToGetPostsArgsInterface
{
    public function apply(GetPostsConfigInterface $config, array $args): array
    {
        return [...$args, 'post_type' => $config->getPostTypes()];
    }
}
