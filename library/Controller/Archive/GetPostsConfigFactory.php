<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\QueryVars\QueryVarsInterface;
use WpService\Contracts\GetTerms;
use WpService\Contracts\GetThemeMod;

/**
 * Factory class for creating GetPostsConfig instances
 */
class GetPostsConfigFactory
{
    /**
     * Constructor
     */
    public function __construct(
        private array $data,
        private FilterConfigInterface $filterConfig,
        private QueryVarsInterface $queryVars,
        private GetThemeMod&GetTerms $wpService,
    ) {}

    /**
     * Create a GetPostsConfig instance
     *
     * @return GetPostsConfigInterface
     */
    public function create(): GetPostsConfigInterface
    {
        return (new GetPostsConfigBuilder())
            ->setPostTypes([(new Mappers\GetPostsConfig\MapPostTypeFromData())->map($this->data)])
            ->setFacettingEnabled((new Mappers\GetPostsConfig\MapIsFacettingFromData())->map($this->data))
            ->setOrderBy((new Mappers\GetPostsConfig\MapOrderByFromData())->map($this->data))
            ->setOrder((new Mappers\GetPostsConfig\MapOrderFromData())->map($this->data))
            ->setPerPage((new Mappers\GetPostsConfig\MapPostsPerPageFromData($this->wpService))->map($this->data))
            ->setDateSource((new Mappers\GetPostsConfig\MapDateSourceFromData())->map($this->data))
            ->setTerms((new Mappers\GetPostsConfig\MapTermsFromData(
                $this->filterConfig,
                $this->queryVars,
                $this->wpService,
            ))->map($this->data))
            ->setCurrentPage((new Mappers\GetPostsConfig\MapCurrentPageFromGetParams(
                $_GET,
                $this->queryVars,
            ))->map($this->data))
            ->build();
    }
}
