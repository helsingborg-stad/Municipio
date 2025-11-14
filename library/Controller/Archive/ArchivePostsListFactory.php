<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\QueryVars\QueryVars;
use WpService\WpService;

class ArchivePostsListFactory
{
    private QueryVars $queryVars;

    public function __construct(private WpService $wpService)
    {
    }

    /**
     * Create a PostsList instance
     *
     * @param array $data
     * @param \WP_Taxonomy[] $wpTaxonomies
     * @return \Municipio\PostsList\PostsList
     */
    public function create(
        array $data,
        array $wpTaxonomies
    ): \Municipio\PostsList\PostsList {
        return new \Municipio\PostsList\PostsList(
            $this->createGetPostsConfig($data),
            (new AppearanceConfigFactory())->create($data),
            $this->getFilterConfig($data),
            $wpTaxonomies,
            new \Municipio\PostsList\GetPosts\WpQueryFactory(),
            $this->getQueryVars(),
            $this->wpService,
        );
    }

    /**
     * Create GetPostsConfig instance
     *
     * @param array $data
     * @return GetPostsConfigInterface
     */
    private function createGetPostsConfig(array $data): GetPostsConfigInterface
    {
        return (new GetPostsConfigFactory($data, $this->getFilterConfig($data), $this->getQueryVars(), $this->wpService))->create();
    }

    private function getFilterConfig(array $data): \Municipio\PostsList\Config\FilterConfig\FilterConfigInterface
    {
        return (new FilterConfigFactory($data, $this->wpService))->create();
    }

    private function getQueryVars(): \Municipio\PostsList\QueryVars\QueryVarsInterface
    {
        if (!isset($this->queryVars)) {
            $this->queryVars = new \Municipio\PostsList\QueryVars\QueryVars('archive_');
        }

        return $this->queryVars;
    }
}
