<?php

namespace Municipio\PostsList;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigUsingGetParamsDecorator;
use Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams\GetTermsFromGetParams;
use Municipio\PostsList\ConfigMapper\PostsListConfigDTOInterface;
use Municipio\PostsList\GetPosts\WpQueryFactory;
use Municipio\PostsList\QueryVars\QueryVars;
use WpService\WpService;

class PostsListFactory implements PostsListFactoryInterface
{
    public function __construct(
        private WpService $wpService,
        private \wpdb $wpdb,
    ) {}

    public function create(PostsListConfigDTOInterface $postsListConfigDTO): PostsListInterface
    {
        $queryVars = new QueryVars($postsListConfigDTO->getQueryVarsPrefix());
        return new PostsList(
            new GetPostsConfigUsingGetParamsDecorator(
                $postsListConfigDTO->getGetPostsConfig(),
                $_GET,
                $queryVars,
                new GetTermsFromGetParams(
                    $_GET,
                    $postsListConfigDTO->getFilterConfig(),
                    $queryVars->getPrefix(),
                    $this->wpService,
                ),
            ),
            $postsListConfigDTO->getAppearanceConfig(),
            $postsListConfigDTO->getFilterConfig(),
            new WpQueryFactory(),
            $queryVars,
            $this->wpService,
            $this->wpdb,
        );
    }
}
