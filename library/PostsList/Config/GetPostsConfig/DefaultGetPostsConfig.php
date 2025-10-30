<?php

namespace Municipio\PostsList\Config\GetPostsConfig;

use Municipio\PostsList\Config\PostsListAppearanceConfig\DefaultPostsListAppearanceConfig;
use Municipio\PostsList\Config\PostsListAppearanceConfig\PostsListAppearanceConfigInterface;

class DefaultGetPostsConfig implements GetPostsConfigInterface
{
    public function getPostTypes(): array
    {
        return ['post'];
    }

    public function getPostsPerPage(): int
    {
        return 10;
    }
}
