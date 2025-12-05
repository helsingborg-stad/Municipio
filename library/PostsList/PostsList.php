<?php

declare(strict_types=1);

namespace Municipio\PostsList;

use AcfService\Contracts\GetField;
use AcfService\Implementations\NativeAcfService;
use Municipio\Helper\Post;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\AnyPostHasImage\AnyPostHasImageInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigWithPlaceholderImage;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
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
    /** @var PostObjectInterface[] */
    private array $posts;
    private WP_Query $wpQuery;
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
     * @param QueryVarRegistrarInterface $queryVarsRegistrar
     * @param GetField $acfService
     * @param AnyPostHasImageInterface $anyPostHasImageService
     *
     * @mago-expect lint:excessive-parameter-list
     */
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private AppearanceConfigInterface $providedAppearanceConfig,
        private FilterConfigInterface $filterConfig,
        private array $wpTaxonomies,
        private WpQueryFactoryInterface $wpQueryFactory,
        private QueryVarsInterface $queryVars,
        private WpService $wpService,
        private \wpdb $wpdb,
        private GetField $acfService = new NativeAcfService(),
        private AnyPostHasImageInterface $anyPostHasImageService = new \Municipio\PostsList\AnyPostHasImage\AnyPostHasImage(),
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
            'posts' => $this->getPosts(),
            'appearanceConfig' => $this->getAppearanceConfig(),
            'filterConfig' => $this->filterConfig,
            'getTags' => (new ViewCallableProviders\GetTagsComponentArguments(
                $this->getPosts(),
                $this->getAppearanceConfig()->getTaxonomiesToDisplay(),
                $this->wpService,
                $this->acfService,
            ))->getCallable(),
            'getExcerpt' => (new ViewCallableProviders\GetExcerpt($this->wpService))->getCallable(),
            'getReadingTime' => (new ViewCallableProviders\GetReadingTime($this->getAppearanceConfig()))->getCallable(),
            'showDateBadge' => (new ViewCallableProviders\ShowDateBadge($this->getPosts()))->getCallable(),
            'getParentColumnClasses' => (new ViewCallableProviders\GetParentColumnClasses())->getCallable(),
            'getPostColumnClasses' => (new ViewCallableProviders\GetPostColumnClasses($this->getAppearanceConfig()))->getCallable(),
            'getDateTimestamp' => (new ViewCallableProviders\GetDateTimestamp(
                $this->getAppearanceConfig()->getDateSource(),
                $this->getPosts(),
                $this->wpdb,
            ))->getCallable(),
            'getDateFormat' => (new ViewCallableProviders\GetDateFormat($this->getAppearanceConfig()->getDateFormat()))->getCallable(),
            // Table view utilities
            'getTableComponentArguments' => (new ViewCallableProviders\Table\GetTableComponentArguments(
                $this->getPosts(),
                $this->getAppearanceConfig(),
                $this->wpService,
                $this->acfService,
            ))->getCallable(),
            // Schema Project view utilities
            'getSchemaProjectProgressLabel' => (new ViewCallableProviders\Schema\Project\GetProgressLabel())->getCallable(),
            'getSchemaProjectProgressPercentage' => (new ViewCallableProviders\Schema\Project\GetProgressPercentage())->getCallable(),
            'getSchemaProjectTechnologyTerms' => (new ViewCallableProviders\GetTermsAsString(
                $this->getPosts(),
                ['project_meta_technology'],
                $this->wpService,
                ' / ',
            ))->getCallable(),
            'getSchemaProjectCategoryTerms' => (new ViewCallableProviders\GetTermsAsString(
                $this->getPosts(),
                ['project_meta_category'],
                $this->wpService,
                ' / ',
            ))->getCallable(),
            // Schema Event view utilities
            'getSchemaEventPriceRange' => (new ViewCallableProviders\Schema\Event\GetPriceRange())->getCallable(),
            'getSchemaEventPlaceName' => (new ViewCallableProviders\Schema\Event\GetPlaceName())->getCallable(),
            'getSchemaEventDate' => (new ViewCallableProviders\Schema\Event\GetDate())->getCallable(),
            'getSchemaEventDateBadgeDate' => (new ViewCallableProviders\Schema\Event\GetDatebadgeDate())->getCallable(),
            // Filter utilities
            'getTaxonomyFilterSelectComponentArguments' => (new ViewCallableProviders\Filter\GetTaxonomyFiltersSelectComponentArguments(
                $this->filterConfig,
                $this->getPostsConfig,
                $this->wpService,
                $this->wpTaxonomies,
                $this->queryVars->getPrefix(),
            ))->getCallable(),
            'getFilterFormSubmitButtonArguments' => (new ViewCallableProviders\Filter\GetFilterSubmitButtonArguments(
                $this->getPostsConfig,
                $this->wpService,
            ))->getCallable(),
            'getFilterFormResetButtonArguments' => (new ViewCallableProviders\Filter\GetFilterResetButtonArguments(
                $this->getPostsConfig,
                $this->filterConfig,
                $this->wpService,
            ))->getCallable(),
            'getTextSearchFieldArguments' => (new ViewCallableProviders\Filter\GetTextSearchFieldArguments(
                $this->getPostsConfig,
                $this->queryVars->getSearchParameterName(),
                $this->wpService,
            ))->getCallable(),
            'getDateFilterFieldArguments' => (new ViewCallableProviders\Filter\GetDateFilterFieldArguments(
                $this->getPostsConfig,
                $this->wpService,
                $this->queryVars->getDateFromParameterName(),
                $this->queryVars->getDateToParameterName(),
            ))->getCallable(),
            // Pagination utilities
            'getPaginationComponentArguments' => (new ViewCallableProviders\Pagination\GetPaginationComponentArguments(
                $this->getWpQuery()->max_num_pages,
                $this->getPostsConfig->getPage(),
                $this->queryVars->getPaginationParameterName(),
            ))->getCallable(),
        ];
    }

    /**
     * Get WP_Query instance
     */
    private function getWpQuery(): WP_Query
    {
        if (!isset($this->wpQuery)) {
            $args = $this->getPostsArgs();
            $this->wpQuery = $this->wpQueryFactory::create($args);
            $this->wpQuery->get_posts();
        }

        return $this->wpQuery;
    }

    /**
     * Get posts args based on config
     */
    private function getPostsArgs(): array
    {
        return (new MapPostArgsFromPostsListConfig($this->getPostsConfig, $this->filterConfig))->getPostsArgs();
    }

    /**
     * Get posts based on config
     *
     * @return PostObjectInterface[]
     */
    private function getPosts(): array
    {
        if (!isset($this->posts)) {
            $this->posts = array_map([Post::class, 'convertWpPostToPostObject'], $this->getWpQuery()->posts);
        }

        return $this->posts;
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
        $this->appearanceConfig = new AppearanceConfigWithPlaceholderImage(
            $shouldDisplayPlaceholderImage,
            $this->wpService,
            $this->providedAppearanceConfig,
        );

        return $this->appearanceConfig;
    }
}
