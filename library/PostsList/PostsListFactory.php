<?php

declare(strict_types=1);

namespace Municipio\PostsList;

use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigUsingGetParamsDecorator;
use Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams\GetTermsFromGetParams;
use Municipio\PostsList\ConfigMapper\PostsListConfigDTOInterface;
use Municipio\PostsList\GetPosts\WpQueryFactory;
use Municipio\PostsList\QueryVars\QueryVars;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use WpService\WpService;

class PostsListFactory implements PostsListFactoryInterface
{
    public function __construct(
        private WpService $wpService,
        private \wpdb $wpdb,
        private SchemaToPostTypeResolverInterface $schemaToPostTypeResolver,
    ) {}

    public function create(PostsListConfigDTOInterface $postsListConfigDTO): PostsListInterface
    {
        $taxonomies = array_map(
            static fn(TaxonomyFilterConfig $taxonomyFilterConfig) => $taxonomyFilterConfig->getTaxonomy()->name,
            $postsListConfigDTO->getFilterConfig()->getTaxonomiesEnabledForFiltering(),
        );
        $queryVars = new QueryVars($postsListConfigDTO->getQueryVarsPrefix(), $taxonomies);

        // Decorate GetPostsConfig.
        $getPostsConfig = $postsListConfigDTO->getGetPostsConfig();
        $getPostsConfig = new GetPostsConfigUsingGetParamsDecorator($getPostsConfig, $_GET, $queryVars, new GetTermsFromGetParams($_GET, $postsListConfigDTO->getFilterConfig(), $queryVars->getPrefix(), $this->wpService));
        $getPostsConfig = new \Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigWithPassedSchemaEventsFilteredOut($getPostsConfig, $this->schemaToPostTypeResolver);

        return new PostsList(
            $getPostsConfig,
            $postsListConfigDTO->getAppearanceConfig(),
            $postsListConfigDTO->getFilterConfig(),
            new WpQueryFactory(),
            $queryVars,
            $this->wpService,
            $this->wpdb,
        );
    }
}
