<?php

namespace Municipio\PostsList;

use AcfService\Contracts\GetField;
use AcfService\Implementations\NativeAcfService;
use Municipio\Helper\Post;
use Municipio\HooksRegistrar\Hookable;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\AnyPostHasImage\AnyPostHasImageInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigWithPlaceholderImage;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\AbstractDecoratedGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams\GetTermsFromGetParams;
use Municipio\PostsList\GetPosts\MapPostArgsFromPostsListConfig;
use Municipio\PostsList\GetPosts\WpQueryFactoryInterface;
use Municipio\PostsList\QueryVarRegistrar\QueryVarRegistrarInterface;
use Municipio\PostsList\QueryVars\QueryVarRegistrar\QueryVarRegistrar;
use Municipio\PostsList\QueryVars\QueryVarsInterface;
use PSpell\Config;
use WP_Query;
use WP_Taxonomy;
use WpService\WpService;

/*
 * Posts list main class
 */
class PostsList
{
    private static $instanceCount = 0;
    private WP_Query $wpQuery;

    /**
     * Constructor
     *
     * @param GetPostsConfigInterface $getPostsConfig
     * @param AppearanceConfigInterface $appearanceConfig
     * @param FilterConfigInterface $filterConfig
     * @param array<string, WP_Taxonomy> $wpTaxonomies
     * @param WpService $wpService
     * @param WpQueryFactoryInterface $wpQueryFactory
     * @param QueryVarRegistrarInterface $querVarsRegistrar
     * @param GetField $acfService
     * @param AnyPostHasImageInterface $anyPostHasImageService
     */
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private AppearanceConfigInterface $appearanceConfig,
        private FilterConfigInterface $filterConfig,
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
            'filterConfig'                              => $this->filterConfig,
            'getTags'                                   => (new ViewCallableProviders\GetTagsComponentArguments($this->getPosts(), $this->appearanceConfig->getTaxonomiesToDisplay(), $this->wpService, $this->acfService))->getCallable(),
            'getExcerptWithoutLinks'                    => (new ViewCallableProviders\GetExcerptWithoutLinks())->getCallable(),
            'getReadingTime'                            => (new ViewCallableProviders\GetReadingTime($this->appearanceConfig))->getCallable(),
            'showDateBadge'                             => (new ViewCallableProviders\ShowDateBadge($this->getPosts()))->getCallable(),
            'getParentColumnClasses'                    => (new ViewCallableProviders\GetParentColumnClasses())->getCallable(),
            'getPostColumnClasses'                      => (new ViewCallableProviders\GetPostColumnClasses($this->getAppearanceConfig()))->getCallable(),

            // Table view utilities
            'getTableComponentArguments'                => (new ViewCallableProviders\Table\GetTableComponentArguments($this->getPosts(), $this->appearanceConfig, $this->wpService, $this->acfService))->getCallable(),

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
            'getTaxonomyFilterSelectComponentArguments' => (new ViewCallableProviders\Filter\GetTaxonomyFiltersSelectComponentArguments($this->filterConfig, $this->getPostsConfig(), $this->wpService, $this->wpTaxonomies, $this->queryVars->getPrefix()))->getCallable(),
            'getFilterFormSubmitButtonArguments'        => (new ViewCallableProviders\Filter\GetFilterSubmitButtonArguments($this->getPostsConfig(), $this->wpService))->getCallable(),
            'getFilterFormResetButtonArguments'         => (new ViewCallableProviders\Filter\GetFilterResetButtonArguments($this->getPostsConfig(), $this->filterConfig, $this->wpService))->getCallable(),
            'getTextSearchFieldArguments'               => (new ViewCallableProviders\Filter\GetTextSearchFieldArguments($this->getPostsConfig(), $this->wpService))->getCallable(),
            'getDateFilterFieldArguments'               => (new ViewCallableProviders\Filter\GetDateFilterFieldArguments($this->getPostsConfig(), $this->wpService))->getCallable(),

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
            $this->getPostsConfig(),
            $this->wpService
        ))->getPosts();
    }

    /**
     * Get decorated getPostsConfig with current page
     */
    private function getPostsConfig(): GetPostsConfigInterface
    {
        $currentPage = $_GET[$this->queryVars->getPaginationParameterName()] ?? 1;
        $terms       = (new GetTermsFromGetParams($_GET, $this->filterConfig, $this->queryVars->getPrefix(), $this->wpService))->getTerms();
        return new class ($this->getPostsConfig, $currentPage, $terms) extends AbstractDecoratedGetPostsConfig {
            /**
             * Constructor
             */
            public function __construct(
                protected GetPostsConfigInterface $innerConfig,
                private int $currentPage,
                private array $terms
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
        };
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
        $shouldDisplayPlaceholderImage = $this->anyPostHasImageService->check(...$this->getPosts());
        return new AppearanceConfigWithPlaceholderImage(
            $shouldDisplayPlaceholderImage,
            $this->appearanceConfig
        );
    }

    /**
     * Get the terms for the posts configuration.
     *
     * @return WP_Term[]
     */
    private function getTermsForPostsConfig(): array
    {
        $params = $_GET;
        $terms  = [];

        if (empty($params) || !is_array($params)) {
            return [];
        }

        $taxonomiesAvailableInFilters = $this->filterConfig->getTaxonomiesEnabledForFiltering();
        $taxonomiesFromGetParams      = array_filter(array_keys($params), function ($key) use ($taxonomiesAvailableInFilters) {
            $cleanKey = rtrim(urldecode($key), '[]');
            $cleanKey = str_replace($this->queryVars->getPrefix(), '', $cleanKey);
            return in_array($cleanKey, $taxonomiesAvailableInFilters);
        });

        $termSlugs = [];

        foreach ($taxonomiesFromGetParams as $taxonomy) {
            if (isset($params[$taxonomy])) {
                $termSlugs  = array_merge($termSlugs, (array) $params[$taxonomy]);
                $foundTerms = $this->wpService->getTerms([
                    'taxonomy'   => str_replace($this->queryVars->getPrefix(), '', $taxonomy),
                    'hide_empty' => false,
                    'slug'       => $termSlugs,
                ]);

                if (is_array($foundTerms)) {
                    $terms = array_merge($terms, $foundTerms);
                }
            }
        }

        return $terms;
    }
}
