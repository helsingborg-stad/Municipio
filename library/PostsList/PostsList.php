<?php

namespace Municipio\PostsList;

use AcfService\Contracts\GetField;
use AcfService\Implementations\NativeAcfService;
use Municipio\Helper\Post;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\AnyPostHasImage\AnyPostHasImageInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigWithPlaceholderImage;
use Municipio\PostsList\Config\FilterConfig\AbstractDecoratedFilterConfig;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\AbstractDecoratedGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetParameterFromGetParams\GetParameterFromGetParams;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetSearchFromGetParams\GetSearchFromGetParams;
use Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams\GetTermsFromGetParams;
use Municipio\PostsList\GetPosts\MapPostArgsFromPostsListConfig;
use Municipio\PostsList\GetPosts\WpQueryFactoryInterface;
use Municipio\PostsList\QueryVarRegistrar\QueryVarRegistrarInterface;
use Municipio\PostsList\QueryVars\QueryVarRegistrar\QueryVarRegistrar;
use Municipio\PostsList\QueryVars\QueryVarsInterface;
use WP_Query;
use WP_Taxonomy;
use WpService\WpService;

/*
 * Posts list main class
 */
class PostsList
{
    private WP_Query $wpQuery;
    private FilterConfigInterface $filterConfig;
    private GetPostsConfigInterface $postsConfig;
    private AppearanceConfigInterface $appearanceConfig;
    /**
     * Constructor
     *
     * @param GetPostsConfigInterface $providedGetPostsConfig
     * @param AppearanceConfigInterface $providedAppearanceConfig
     * @param FilterConfigInterface $providedFilterConfig
     * @param array<string, WP_Taxonomy> $wpTaxonomies
     * @param WpService $wpService
     * @param WpQueryFactoryInterface $wpQueryFactory
     * @param QueryVarRegistrarInterface $querVarsRegistrar
     * @param GetField $acfService
     * @param AnyPostHasImageInterface $anyPostHasImageService
     */
    public function __construct(
        private GetPostsConfigInterface $providedGetPostsConfig,
        private AppearanceConfigInterface $providedAppearanceConfig,
        private FilterConfigInterface $providedFilterConfig,
        private array $wpTaxonomies,
        private WpQueryFactoryInterface $wpQueryFactory,
        private QueryVarsInterface $queryVars,
        private WpService $wpService,
        private GetField $acfService = new NativeAcfService(),
        private AnyPostHasImageInterface $anyPostHasImageService = new \Municipio\PostsList\AnyPostHasImage\AnyPostHasImage()
    ) {
        (new QueryVarRegistrar($this->queryVars, $this->wpService))->register();
    }

    /**
     * Get data for rendering
     *
     * @return array
     */
    public function getData(): array
    {
        return [
            'posts'                                     => $this->getPosts(),
            'appearanceConfig'                          => $this->getAppearanceConfig(),
            'filterConfig'                              => $this->getFilterConfig(),
            'getTags'                                   => (new ViewCallableProviders\GetTagsComponentArguments($this->getPosts(), $this->getAppearanceConfig()->getTaxonomiesToDisplay(), $this->wpService, $this->acfService))->getCallable(),
            'getExcerptWithoutLinks'                    => (new ViewCallableProviders\GetExcerptWithoutLinks())->getCallable(),
            'getReadingTime'                            => (new ViewCallableProviders\GetReadingTime($this->getAppearanceConfig()))->getCallable(),
            'showDateBadge'                             => (new ViewCallableProviders\ShowDateBadge($this->getPosts()))->getCallable(),
            'getParentColumnClasses'                    => (new ViewCallableProviders\GetParentColumnClasses())->getCallable(),
            'getPostColumnClasses'                      => (new ViewCallableProviders\GetPostColumnClasses($this->getAppearanceConfig()))->getCallable(),

            // Table view utilities
            'getTableComponentArguments'                => (new ViewCallableProviders\Table\GetTableComponentArguments($this->getPosts(), $this->getAppearanceConfig(), $this->wpService, $this->acfService))->getCallable(),

            // Schema Project view utilities
            'getSchemaProjectProgressLabel'             => (new ViewCallableProviders\Schema\Project\GetProgressLabel())->getCallable(),
            'getSchemaProjectProgressPercentage'        => (new ViewCallableProviders\Schema\Project\GetProgressPercentage())->getCallable(),
            'getSchemaProjectTechnologyTerms'           => (new ViewCallableProviders\GetTermsAsString($this->getPosts(), ['project_meta_technology'], $this->wpService, ' / '))->getCallable(),
            'getSchemaProjectCategoryTerms'             => (new ViewCallableProviders\GetTermsAsString($this->getPosts(), ['project_meta_category'], $this->wpService, ' / '))->getCallable(),

            // Schema Event view utilities
            'getSchemaEventPriceRange'                  => (new ViewCallableProviders\Schema\Event\GetPriceRange())->getCallable(),
            'getSchemaEventPlaceName'                   => (new ViewCallableProviders\Schema\Event\GetPlaceName())->getCallable(),
            'getSchemaEventDate'                        => (new ViewCallableProviders\Schema\Event\GetDate())->getCallable(),
            'getSchemaEventDateBadgeDate'               => (new ViewCallableProviders\Schema\Event\GetDatebadgeDate())->getCallable(),

            // Filter utilities
            'getTaxonomyFilterSelectComponentArguments' => (new ViewCallableProviders\Filter\GetTaxonomyFiltersSelectComponentArguments($this->getFilterConfig(), $this->getPostsConfig(), $this->wpService, $this->wpTaxonomies, $this->queryVars->getPrefix()))->getCallable(),
            'getFilterFormSubmitButtonArguments'        => (new ViewCallableProviders\Filter\GetFilterSubmitButtonArguments($this->getPostsConfig(), $this->wpService))->getCallable(),
            'getFilterFormResetButtonArguments'         => (new ViewCallableProviders\Filter\GetFilterResetButtonArguments($this->getPostsConfig(), $this->getFilterConfig(), $this->wpService))->getCallable(),
            'getTextSearchFieldArguments'               => (new ViewCallableProviders\Filter\GetTextSearchFieldArguments($this->getPostsConfig(), $this->queryVars->getSearchParameterName(), $this->wpService))->getCallable(),
            'getDateFilterFieldArguments'               => (new ViewCallableProviders\Filter\GetDateFilterFieldArguments($this->getPostsConfig(), $this->wpService, $this->queryVars->getDateFromParameterName(), $this->queryVars->getDateToParameterName()))->getCallable(),

            // Pagination utilities
            'getPaginationComponentArguments'           => (new ViewCallableProviders\Pagination\GetPaginationComponentArguments($this->getTotalNumberOfPages(), $this->getPostsConfig()->getPage(), $this->queryVars->getPaginationParameterName()))->getCallable(),
        ];
    }

    /**
     * Get WP_Query instance
     */
    private function getWpQuery(): WP_Query
    {
        if (!isset($this->wpQuery)) {
            $args          = $this->getPostsArgs();
            $this->wpQuery = $this->wpQueryFactory::create($args);
        }

        return $this->wpQuery;
    }

    /**
     * Get posts args based on config
     */
    private function getPostsArgs(): array
    {
        return (new MapPostArgsFromPostsListConfig(
            $this->getPostsConfig()
        ))->getPostsArgs();
    }

    /**
     * Get filter configuration
     *
     * @return FilterConfigInterface
     */
    private function getFilterConfig(): FilterConfigInterface
    {
        if (isset($this->filterConfig)) {
            return $this->filterConfig;
        }

        $showReset = false;
        $terms     = (new GetTermsFromGetParams($_GET, $this->providedFilterConfig, $this->queryVars->getPrefix(), $this->wpService))->getTerms();
        $search    = (new GetParameterFromGetParams())->getParam($_GET, $this->queryVars->getSearchParameterName()) ?? '';
        $dateFrom  = (new GetParameterFromGetParams())->getParam($_GET, $this->queryVars->getDateFromParameterName()) ?? '';
        $dateTo    = (new GetParameterFromGetParams())->getParam($_GET, $this->queryVars->getDateToParameterName()) ?? '';

        if (!empty($terms) || !empty($search) || !empty($dateFrom) || !empty($dateTo)) {
            $showReset = true;
        }

        $this->filterConfig = new class ($this->providedFilterConfig, $showReset) extends AbstractDecoratedFilterConfig{
            /**
             * Constructor
             */
            public function __construct(protected FilterConfigInterface $innerConfig, private bool $showReset)
            {
            }

            /**
             * @inheritDoc
             */
            public function showReset(): bool
            {
                return $this->showReset;
            }
        };

        return $this->filterConfig;
    }

    /**
     * Get decorated getPostsConfig with current page
     */
    private function getPostsConfig(): GetPostsConfigInterface
    {
        if (isset($this->postsConfig)) {
            return $this->postsConfig;
        }

        $currentPage       = $_GET[$this->queryVars->getPaginationParameterName()] ?? 1;
        $terms             = (new GetTermsFromGetParams($_GET, $this->getFilterConfig(), $this->queryVars->getPrefix(), $this->wpService))->getTerms();
        $search            = (new GetParameterFromGetParams())->getParam($_GET, $this->queryVars->getSearchParameterName()) ?? '';
        $dateFrom          = (new GetParameterFromGetParams())->getParam($_GET, $this->queryVars->getDateFromParameterName()) ?? '';
        $dateTo            = (new GetParameterFromGetParams())->getParam($_GET, $this->queryVars->getDateToParameterName()) ?? '';
        $this->postsConfig = new class ($this->providedGetPostsConfig, $currentPage, $terms, $search, $dateFrom, $dateTo) extends AbstractDecoratedGetPostsConfig {
            /**
             * Constructor
             */
            public function __construct(
                protected GetPostsConfigInterface $innerConfig,
                private int $currentPage,
                private array $terms,
                private string $search,
                private string $dateFrom,
                private string $dateTo
            ) {
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
            public function getTerms(): array
            {
                return $this->terms;
            }

            /**
             * @inheritDoc
             */
            public function getSearch(): string
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

        return $this->postsConfig;
    }

    /**
     * Get posts based on config
     *
     * @return PostObjectInterface[]
     */
    private function getPosts(): array
    {
        return array_map(fn($wpPost) => Post::convertWpPostToPostObject($wpPost), (new GetPosts\GetPostsUsingWpQuery($this->getWpQuery()))->getPosts());
    }

    /**
     * Get total number of pages based on WP_Query
     */
    private function getTotalNumberOfPages(): int
    {
        $wpQuery = $this->getWpQuery();

        if (!isset($wpQuery->max_num_pages)) {
            $this->wpQuery->get_posts();
        }

        return $wpQuery->max_num_pages;
    }

    /**
     * Get appearance config with placeholder image logic
     *
     * @return AppearanceConfigInterface
     */
    private function getAppearanceConfig(): AppearanceConfigInterface
    {
        if (isset($this->appearanceConfig)) {
            return $this->appearanceConfig;
        }

        $shouldDisplayPlaceholderImage = $this->anyPostHasImageService->check(...$this->getPosts());
        $this->appearanceConfig        = new AppearanceConfigWithPlaceholderImage(
            $shouldDisplayPlaceholderImage,
            $this->wpService,
            $this->providedAppearanceConfig
        );

        return $this->appearanceConfig;
    }
}
