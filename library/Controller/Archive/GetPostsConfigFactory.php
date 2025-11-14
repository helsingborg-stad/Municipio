<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetParameterFromGetParams\GetParameterFromGetParams;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use Municipio\PostsList\QueryVars\QueryVarsInterface;
use WP_Query;
use WP_Term;
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
        $terms              = $this->resolveTerms($this->data['wpQuery'] ?? null);
        $postType           = [$this->getPostType()];
        $isFacettingEnabled = $this->getFacettingType();
        $orderBy            = $this->data['archiveProps']->orderBy ?? 'post_date';
        $perPage            = (int)$this->wpService->getThemeMod('archive_' . $this->getPostType() . '_post_count', $this->wpService->getThemeMod('archive_post_post_count', 10));
        $dateSource         = $this->data['archiveProps']->dateField ?? 'post_date';
        $order              = (isset($this->data['archiveProps']->orderDirection) && strtoupper($this->data['archiveProps']->orderDirection) === 'ASC')
            ? OrderDirection::ASC
            : OrderDirection::DESC;
        $currentPage        = $_GET[$this->queryVars->getPaginationParameterName()] ?? 1;
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
                private array $postType,
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
                return $this->postType;
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

    /**
     * Get the post type for the archive
     *
     * @return string
     */
    private function getPostType(): string
    {
        return !empty($this->data['postType']) ? $this->data['postType'] : 'page';
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @return  boolean                 True or false val.
     */
    private function getFacettingType(): bool
    {
        if (!is_object($this->data['archiveProps'])) {
            $this->data['archiveProps'] = (object) [];
        }

        if (!isset($this->data['archiveProps']->filterType) || is_null($this->data['archiveProps']->filterType)) {
            $this->data['archiveProps']->filterType = false;
        }

        return (bool) $this->data['archiveProps']->filterType;
    }

    /**
     * Get the current term from the query
     *
     * @param \WP_Query|null $wpQuery
     * @return \WP_Term|null
     */
    private function getCurrentTerm(?WP_Query $wpQuery = null): ?WP_Term
    {
        if (is_null($wpQuery)) {
            return null;
        }

        if (!$wpQuery->is_tax && !$wpQuery->is_category && !$wpQuery->is_tag) {
            return null;
        }

        return is_a($wpQuery->queried_object, WP_Term::class) ?  $wpQuery->queried_object : null;
    }

    /**
     * Resolve terms from the current query
     *
     * @param \WP_Query|null $wpQuery
     * @return \WP_Term[]
     */
    private function resolveTerms(?\WP_Query $wpQuery = null): array
    {
        $currentTerm = $this->getCurrentTerm($wpQuery);

        return !is_null($currentTerm) ? [$currentTerm] :  $this->resolveTermsFromQueryParams();
    }

    /**
     * Resolve terms from query parameters
     *
     * @return \WP_Term[]
     */
    private function resolveTermsFromQueryParams(): array
    {
        return (new \Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams\GetTermsFromGetParams(
            $_GET,
            $this->filterConfig,
            $this->queryVars->getPrefix(),
            $this->wpService
        ))->getTerms();
    }
}
