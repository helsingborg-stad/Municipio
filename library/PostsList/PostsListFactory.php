<?php

namespace Municipio\PostsList;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\GetPosts\WpQueryFactory;
use Municipio\PostsList\QueryVars\QueryVars;
use WpService\WpService;

class PostsListFactory implements PostsListFactoryInterface
{
    public function __construct(private WpService $wpService)
    {
    }

    public function create(
        GetPostsConfigInterface $getPostsConfig,
        AppearanceConfigInterface $appearanceConfig,
        FilterConfigInterface $filterConfig,
        string $queryVarsPrefix
    ): PostsList {
        return new PostsList(
            $getPostsConfig,
            $appearanceConfig,
            $filterConfig,
            new WpQueryFactory(),
            new QueryVars($queryVarsPrefix),
            $this->wpService
        );
    }
}
