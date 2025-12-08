<?php

namespace Municipio\PostsList;

use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigUsingGetParamsDecorator;
use Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams\GetTermsFromGetParams;
use Municipio\PostsList\GetPosts\WpQueryFactory;
use Municipio\PostsList\QueryVars\QueryVars;
use WpService\WpService;

class PostsListFactory implements PostsListFactoryInterface
{
    public function __construct(
        private WpService $wpService,
        private \wpdb $wpdb,
    ) {}

    public function create(
        GetPostsConfigInterface $getPostsConfig,
        AppearanceConfigInterface $appearanceConfig,
        FilterConfigInterface $filterConfig,
        string $queryVarsPrefix,
    ): PostsList {
        $queryVars = new QueryVars($queryVarsPrefix);
        return new PostsList(
            new GetPostsConfigUsingGetParamsDecorator(
                $getPostsConfig,
                $_GET,
                $queryVars,
                new GetTermsFromGetParams($_GET, $filterConfig, $queryVars->getPrefix(), $this->wpService),
            ),
            $appearanceConfig,
            $filterConfig,
            new WpQueryFactory(),
            $queryVars,
            $this->wpService,
            $this->wpdb,
        );
    }
}
