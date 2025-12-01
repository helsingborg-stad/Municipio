<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\QueryVars\QueryVars;
use WpService\WpService;

/**
 * Factory class for creating PostsList instances for archives
 */
class ArchivePostsListFactory
{
    private QueryVars $queryVars;

    /**
     * Constructor
     */
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
            $this->createGetPostsConfig($data, $wpTaxonomies),
            (new AppearanceConfigFactory())->create($data),
            $this->getFilterConfig($data, $wpTaxonomies),
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
     * @param \WP_Taxonomy[] $wpTaxonomies
     * @return GetPostsConfigInterface
     */
    private function createGetPostsConfig(array $data, array $wpTaxonomies): GetPostsConfigInterface
    {
        return (new GetPostsConfigFactory($data, $this->getFilterConfig($data, $wpTaxonomies), $this->getQueryVars(), $this->wpService))->create();
    }

    /**
     * Create FilterConfig instance
     *
     * @param array $data
     * @param \WP_Taxonomy[] $wpTaxonomies
     * @return \Municipio\PostsList\Config\FilterConfig\FilterConfigInterface
     */
    private function getFilterConfig(array $data, $wpTaxonomies): \Municipio\PostsList\Config\FilterConfig\FilterConfigInterface
    {
        return (new FilterConfigFactory($data, $wpTaxonomies, $this->wpService, $this->getQueryVars()))->create();
    }

    /**
     * Get QueryVars instance
     *
     * @return \Municipio\PostsList\QueryVars\QueryVarsInterface
     */
    private function getQueryVars(): \Municipio\PostsList\QueryVars\QueryVarsInterface
    {
        if (!isset($this->queryVars)) {
            $this->queryVars = new \Municipio\PostsList\QueryVars\QueryVars('archive_');
        }

        return $this->queryVars;
    }
}
