<?php

namespace Municipio\PostsList\Config;

use Municipio\PostsList\Config\PostsListAppearanceConfig\DefaultPostsListAppearanceConfig;
use Municipio\PostsList\Config\PostsListAppearanceConfig\PostsListAppearanceConfigInterface;

class DefaultPostsListConfig implements PostsListConfigInterface
{
    public function getPostTypes(): array
    {
        return ['post'];
    }

    public function getAppearanceConfig(): PostsListAppearanceConfigInterface
    {
        return new DefaultPostsListAppearanceConfig();
    }

    public function getPostsPerPage(): int
    {
        return 10;
    }
}
