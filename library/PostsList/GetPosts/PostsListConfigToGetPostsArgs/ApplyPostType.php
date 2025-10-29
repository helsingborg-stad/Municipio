<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\PostsListConfigInterface;

class ApplyPostType implements ApplyPostsListConfigToGetPostsArgsInterface
{
    public function apply(PostsListConfigInterface $config, array $args): array
    {
        return [...$args, 'post_type' => $config->getPostTypes()];
    }
}
