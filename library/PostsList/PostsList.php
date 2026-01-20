<?php

declare(strict_types=1);

namespace Municipio\PostsList;

use AcfService\Contracts\GetField;
use AcfService\Implementations\NativeAcfService;
use Municipio\Helper\Post;
use Municipio\PostObject\PostObjectInterface;
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
use WpService\WpService;

/*
 * Posts list main class
 */
class PostsList implements PostsListInterface
{
    /** @var PostObjectInterface[] */
    private array $posts;
    private WP_Query $wpQuery;

    /**
     * Constructor
     *
     * @param GetPostsConfigInterface $getPostsConfig
     * @param AppearanceConfigInterface $providedAppearanceConfig
     * @param FilterConfigInterface $providedFilterConfig
     * @param WpService $wpService
     * @param WpQueryFactoryInterface $wpQueryFactory
     * @param QueryVarRegistrarInterface $queryVarsRegistrar
     * @param GetField $acfService
     *
     * @mago-expect lint:excessive-parameter-list
     */
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private AppearanceConfigInterface $appearanceConfig,
        private FilterConfigInterface $filterConfig,
        private WpQueryFactoryInterface $wpQueryFactory,
        private QueryVarsInterface $queryVars,
        private WpService $wpService,
        private \wpdb $wpdb,
        private GetField $acfService = new NativeAcfService(),
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
            'appearanceConfig' => $this->appearanceConfig,
            'filterConfig' => $this->filterConfig,
            'id' => $this->getId(),
            'getTags' => (new ViewCallableProviders\GetTagsComponentArguments($this->getPosts(), $this->appearanceConfig->getTaxonomiesToDisplay(), $this->wpService, $this->acfService))->getCallable(),
            'getExcerpt' => (new ViewCallableProviders\GetExcerpt($this->wpService))->getCallable(),
            'getReadingTime' => (new ViewCallableProviders\GetReadingTime($this->appearanceConfig))->getCallable(),
            'showDateBadge' => (new ViewCallableProviders\ShowDateBadge($this->appearanceConfig))->getCallable(),
            'getParentColumnClasses' => (new ViewCallableProviders\GetParentColumnClasses())->getCallable(),
            'getPostColumnClasses' => (new ViewCallableProviders\GetPostColumnClasses($this->appearanceConfig))->getCallable(),
            'getDateTimestamp' => (new ViewCallableProviders\GetDateTimestamp($this->appearanceConfig->getDateSource(), $this->getPosts(), $this->wpdb))->getCallable(),
            'getDateFormat' => (new ViewCallableProviders\GetDateFormat($this->appearanceConfig->getDateFormat()))->getCallable(),
            'shouldDisplayPlaceholderImage' => (new ViewCallableProviders\ShouldDisplayPlaceholderImage(...$this->getPosts()))->getCallable(),
            // Table view utilities
            'getTableComponentArguments' => (new ViewCallableProviders\Table\GetTableComponentArguments($this->getPosts(), $this->appearanceConfig, $this->wpService, $this->acfService))->getCallable(),
            // Schema Project view utilities
            'getSchemaProjectProgressLabel' => (new ViewCallableProviders\Schema\Project\GetProgressLabel())->getCallable(),
            'getSchemaProjectProgressPercentage' => (new ViewCallableProviders\Schema\Project\GetProgressPercentage())->getCallable(),
            'getSchemaProjectTechnologyTerms' => (new ViewCallableProviders\GetTermsAsString($this->getPosts(), ['project_meta_technology'], $this->wpService, ' / '))->getCallable(),
            'getSchemaProjectCategoryTerms' => (new ViewCallableProviders\GetTermsAsString($this->getPosts(), ['project_meta_category'], $this->wpService, ' / '))->getCallable(),
            // Schema Event view utilities
            'getSchemaEventHasMoreOccasions' => (new ViewCallableProviders\Schema\Event\EventHasMoreOccasions())->getCallable(),
            'getEventMoreOccasionsLabel' => fn() => $this->wpService->_x('More occasions available', 'label for more occasions available on event', 'municipio'),
            'getSchemaEventPlaceName' => (new ViewCallableProviders\Schema\Event\GetPlaceName())->getCallable(),
            'getSchemaEventDate' => (new ViewCallableProviders\Schema\Event\GetDate($this->wpService, $this->getPostsConfig->getDateFrom()))->getCallable(),
            'getSchemaEventDateBadgeDate' => (new ViewCallableProviders\Schema\Event\GetDatebadgeDate($this->getPostsConfig->getDateFrom()))->getCallable(),
            'getSchemaEventPermalink' => (new ViewCallableProviders\Schema\Event\GetPermalink($this->getPostsConfig->getDateFrom()))->getCallable(),
            // Schema Exhibition event view utilities
            'getSchemaExhibitionOccasionText' => (new ViewCallableProviders\Schema\ExhibitionEvent\GetOccasionText($this->wpService))->getCallable(),
            // Filter utilities
            'getTaxonomyFilterSelectComponentArguments' => (new ViewCallableProviders\Filter\GetTaxonomyFiltersSelectComponentArguments($this->filterConfig, $this->getPostsConfig, $this->wpService, $this->queryVars->getPrefix()))->getCallable(),
            'getFilterFormSubmitButtonArguments' => (new ViewCallableProviders\Filter\GetFilterSubmitButtonArguments($this->getPostsConfig, $this->wpService))->getCallable(),
            'getFilterFormResetButtonArguments' => (new ViewCallableProviders\Filter\GetFilterResetButtonArguments($this->getPostsConfig, $this->filterConfig, $this->wpService))->getCallable(),
            'getTextSearchFieldArguments' => (new ViewCallableProviders\Filter\GetTextSearchFieldArguments($this->getPostsConfig, $this->queryVars->getSearchParameterName(), $this->wpService))->getCallable(),
            'getDateFilterFieldArguments' => (new ViewCallableProviders\Filter\GetDateFilterFieldArguments($this->getPostsConfig, $this->wpService, $this->queryVars->getDateFromParameterName(), $this->queryVars->getDateToParameterName()))->getCallable(),
            // Pagination utilities
            'paginationEnabled' => fn() => $this->getPostsConfig->paginationEnabled(),
            'getPaginationComponentArguments' => (new ViewCallableProviders\Pagination\GetPaginationComponentArguments($this->getWpQuery()->max_num_pages, $this->getPostsConfig->getPage(), $this->queryVars->getPaginationParameterName(), $this->getId()))->getCallable(),
        ];
    }

    private function getId(): string
    {
        return $this->queryVars->getPrefix() . 'id';
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
        return (new MapPostArgsFromPostsListConfig($this->getPostsConfig, $this->filterConfig, $this->appearanceConfig))->getPostsArgs();
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
}
