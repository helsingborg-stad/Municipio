<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetParameterFromGetParams\GetParameterFromGetParams;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
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
        private GetThemeMod&GetTerms $wpService
    ) {
    }

    /**
     * Create a GetPostsConfig instance
     *
     * @return GetPostsConfigInterface
     */
    public function create(): GetPostsConfigInterface
    {
        $terms              = (new Mappers\MapTermsFromData($this->filterConfig, $this->queryVars, $this->wpService))->map($this->data);
        $postType           = (new Mappers\MapPostTypeFromData())->map($this->data);
        $isFacettingEnabled = (new Mappers\MapIsFacettingFromData())->map($this->data);
        $orderBy            = (new Mappers\MapOrderByFromData())->map($this->data);
        $perPage            = (new Mappers\MapPostsPerPageFromData($postType, $this->wpService))->map($this->data);
        $dateSource         = (new Mappers\MapDateSourceFromData())->map($this->data);
        $order              = (new Mappers\MapOrderFromData())->map($this->data);
        $currentPage        = (new Mappers\MapCurrentPageFromGetParams($_GET, $this->queryVars))->map($this->data);
        $search             = (new GetParameterFromGetParams())->getParam($_GET, $this->queryVars->getSearchParameterName()) ?? '';
        $dateFrom           = (new GetParameterFromGetParams())->getParam($_GET, $this->queryVars->getDateFromParameterName()) ?? '';
        $dateTo             = (new GetParameterFromGetParams())->getParam($_GET, $this->queryVars->getDateToParameterName()) ?? '';

        return new class (
            $postType,
            $isFacettingEnabled,
            $orderBy,
            $order,
            $perPage,
            $dateSource,
            $terms,
            $currentPage,
            $search,
            $dateFrom,
            $dateTo
        ) extends \Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig {
            /**
             * Constructor
             */
            public function __construct(
                private string $postType,
                private bool $isFacettingEnabled,
                private string $orderBy,
                private OrderDirection $order,
                private int $perPage,
                private string $dateSource,
                private array $terms,
                private int $currentPage,
                private string $search,
                private string $dateFrom,
                private string $dateTo
            ) {
            }

            /**
             * @inheritDoc
             */
            public function getPostTypes(): array
            {
                return [$this->postType];
            }

            /**
             * @inheritDoc
             */
            public function getPostsPerPage(): int
            {
                return $this->perPage;
            }

            /**
             * @inheritDoc
             */
            public function isFacettingTaxonomyQueryEnabled(): bool
            {
                return $this->isFacettingEnabled;
            }

            /**
             * @inheritDoc
             */
            public function getOrderBy(): string
            {
                return $this->orderBy;
            }

            /**
             * @inheritDoc
             */
            public function getOrder(): \Municipio\PostsList\Config\GetPostsConfig\OrderDirection
            {
                return $this->order;
            }

            /**
             * @inheritDoc
             */
            public function getDateSource(): string
            {
                return $this->dateSource;
            }

            /**
             * @inheritDoc
             */
            public function getTerms(): array
            {
                return $this->terms;
            }

            /**
             * @inheritDoc
             */
            public function getPage(): int
            {
                return $this->currentPage;
            }

            /**
             * @inheritDoc
             */
            public function getSearch(): ?string
            {
                return $this->search;
            }

            /**
             * @inheritDoc
             */
            public function getDateFrom(): ?string
            {
                return $this->dateFrom;
            }

            /**
             * @inheritDoc
             */
            public function getDateTo(): ?string
            {
                return $this->dateTo;
            }
        };
    }
}
