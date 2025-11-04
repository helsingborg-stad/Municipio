<?php

namespace Municipio\PostsList;

use AcfService\Contracts\GetField;
use AcfService\Implementations\NativeAcfService;
use Municipio\PostObject\PostObjectInterface;
use Municipio\PostsList\AnyPostHasImage\AnyPostHasImageInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigInterface;
use Municipio\PostsList\Config\AppearanceConfig\AppearanceConfigWithPlaceholderImage;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\GetPosts\GetPostsFromPostsListConfig;
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
     * @param WpService $wpService
     * @param GetField $acfService
     * @param AnyPostHasImageInterface $anyPostHasImageService
     */
    public function __construct(
        private GetPostsConfigInterface $getPostsConfig,
        private AppearanceConfigInterface $appearanceConfig,
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
            'posts'                              => $this->getPosts(),
            'config'                             => $this->getAppearanceConfig(),
            'getTags'                            => (new ViewUtilities\GetTagsComponentArguments($this->getPosts(), $this->appearanceConfig->getTaxonomiesToDisplay(), $this->wpService, $this->acfService))->getCallable(),
            'getExcerptWithoutLinks'             => (new ViewUtilities\GetExcerptWithoutLinks())->getCallable(),
            'getReadingTime'                     => (new ViewUtilities\GetReadingTime($this->appearanceConfig))->getCallable(),
            'showDateBadge'                      => (new ViewUtilities\ShowDateBadge($this->getPosts()))->getCallable(),
            'getParentColumnClasses'             => (new ViewUtilities\GetParentColumnClasses())->getCallable(),
            'getPostColumnClasses'               => (new ViewUtilities\GetPostColumnClasses($this->getAppearanceConfig()))->getCallable(),

            // Table view utilities
            'getTableComponentArguments'         => (new ViewUtilities\Table\GetTableComponentArguments($this->getPosts(), $this->appearanceConfig, $this->wpService, $this->acfService))->getCallable(),

            // Schema Project view utilities
            'getSchemaProjectProgressLabel'      => (new ViewUtilities\Schema\Project\GetProgressLabel())->getCallable(),
            'getSchemaProjectProgressPercentage' => (new ViewUtilities\Schema\Project\GetProgressPercentage())->getCallable(),
            'getSchemaProjectTechnologyTerms'    => (new ViewUtilities\GetTermsAsString($this->getPosts(), ['project_meta_technology'], $this->wpService, ' / '))->getCallable(),
            'getSchemaProjectCategoryTerms'      => (new ViewUtilities\GetTermsAsString($this->getPosts(), ['project_meta_category'], $this->wpService, ' / '))->getCallable(),

            // Schema Event view utilities
            'getSchemaEventPriceRange'           => (new ViewUtilities\Schema\Event\GetPriceRange())->getCallable(),
            'getSchemaEventPlaceName'            => (new ViewUtilities\Schema\Event\GetPlaceName())->getCallable(),
            'getSchemaEventDate'                 => (new ViewUtilities\Schema\Event\GetDate())->getCallable(),
            'getSchemaEventDateBadgeDate'        => (new ViewUtilities\Schema\Event\GetDatebadgeDate())->getCallable(),
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
