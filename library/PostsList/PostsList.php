<?php

namespace Municipio\PostsList;

use AcfService\Contracts\GetField;
use AcfService\Implementations\NativeAcfService;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\AnyPostHasImage\AnyPostHasImageInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigWithPlaceholderImage;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\GetPosts\GetPostsFromPostsListConfig;
use WP_Taxonomy;
use WpService\WpService;

/*
 * Posts list main class
 */
class PostsList
{
    /**
     * @var PostObjectInterface[]
     */
    private array $posts;

    /**
     * Constructor
     *
     * @param GetPostsConfigInterface $getPostsConfig
     * @param AppearanceConfigInterface $appearanceConfig
     * @param FilterConfigInterface $filterConfig
     * @param array<string, WP_Taxonomy> $wpTaxonomies
     * @param WpService $wpService
     * @param GetField $acfService
     * @param AnyPostHasImageInterface $anyPostHasImageService
     */
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private AppearanceConfigInterface $appearanceConfig,
        private FilterConfigInterface $filterConfig,
        private array $wpTaxonomies,
        private WpService $wpService,
        private GetField $acfService = new NativeAcfService(),
        private AnyPostHasImageInterface $anyPostHasImageService = new \Municipio\PostsList\AnyPostHasImage\AnyPostHasImage()
    ) {
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
            'getTaxonomyFilterSelectComponentArguments' => (new ViewCallableProviders\Filter\GetTaxonomyFiltersSelectComponentArguments($this->filterConfig, $this->wpService, $this->wpTaxonomies))->getCallable(),
            'getFilterFormSubmitButtonArguments'        => (new ViewCallableProviders\Filter\GetFilterSubmitButtonArguments($this->getPostsConfig, $this->wpService))->getCallable(),
            'getFilterFormResetButtonArguments'         => (new ViewCallableProviders\Filter\GetFilterResetButtonArguments($this->getPostsConfig, $this->filterConfig, $this->wpService))->getCallable(),
            'getTextSearchFieldArguments'               => (new ViewCallableProviders\Filter\GetTextSearchFieldArguments($this->getPostsConfig, $this->wpService))->getCallable(),
        ];
    }

    /**
     * Get posts based on config
     *
     * @return PostObjectInterface[]
     */
    private function getPosts(): array
    {
        if (!isset($this->posts)) {
            $this->posts = (new GetPostsFromPostsListConfig(
                $this->getPostsConfig,
                $this->wpService
            ))->getPosts();
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
        $shouldDisplayPlaceholderImage = $this->anyPostHasImageService->check(...$this->getPosts());
        return new AppearanceConfigWithPlaceholderImage(
            $shouldDisplayPlaceholderImage,
            $this->appearanceConfig
        );
    }
}
