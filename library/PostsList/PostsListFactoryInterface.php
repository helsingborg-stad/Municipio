<?php

namespace Municipio\PostsList;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;

interface PostsListFactoryInterface
{
    /**
     * Create a PostsList instance
     */
    public function create(
        GetPostsConfigInterface $getPostsConfig,
        AppearanceConfigInterface $appearanceConfig,
        FilterConfigInterface $filterConfig,
        string $queryVarsPrefix
    ): PostsList;
}
