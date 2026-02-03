<?php

declare(strict_types=1);

namespace Municipio\Archive\AsyncAttributesProvider;

use Municipio\Controller\Archive\AppearanceConfigFactory;
use Municipio\Controller\Archive\ArchiveDefaults;
use Municipio\Controller\Archive\FilterConfigFactory;
use Municipio\Controller\Archive\GetPostsConfigFactory;
use WpService\Contracts\GetThemeMod;

/**
 * Archive async attributes provider
 *
 * Provides JSON-serializable attributes for archive pages that can be used
 * for async rendering and client-side hydration. This implementation delegates
 * to existing mapper factories to ensure consistency with archive rendering.
 */
class ArchiveAsyncAttributesProvider implements AsyncAttributesProviderInterface
{
    private array $attributes = [];

    /**
     * Constructor
     *
     * @param string $postType The post type of the archive
     * @param object $archiveProps Archive properties from customizer
     * @param GetThemeMod $wpService WordPress service for resolving theme mods
     * @param AppearanceConfigFactory $appearanceConfigFactory Factory for appearance config
     * @param FilterConfigFactory $filterConfigFactory Factory for filter config
     * @param GetPostsConfigFactory $getPostsConfigFactory Factory for get posts config
     */
    public function __construct(
        private string $postType,
        private object $archiveProps,
        private GetThemeMod $wpService,
        private AppearanceConfigFactory $appearanceConfigFactory,
        private FilterConfigFactory $filterConfigFactory,
        private GetPostsConfigFactory $getPostsConfigFactory,
    ) {
        $this->attributes = $this->buildAttributes();
    }

    /**
     * Get async attributes
     *
     * @return array Array of JSON-serializable attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the attributes array from archive data
     *
     * Delegates to existing mapper factories to extract configuration.
     * This ensures consistency between initial archive render and async updates.
     *
     * @return array
     */
    private function buildAttributes(): array
    {
        // Build data array for factories
        $data = [
            'archiveProps' => $this->archiveProps,
            'postType' => $this->postType,
            'wpService' => $this->wpService,
        ];

        // Create configs using existing factories
        $appearanceConfig = $this->appearanceConfigFactory->create($data);
        $filterConfig = $this->filterConfigFactory->create();
        $getPostsConfig = $this->getPostsConfigFactory->create();

        // Extract values from configs (declarative approach)
        return [
            'postType' => $this->postType,
            'queryVarsPrefix' => ArchiveDefaults::QUERY_VARS_PREFIX,
            'dateSource' => $appearanceConfig->getDateSource(),
            'dateFormat' => $appearanceConfig->getDateFormat()->value,
            'design' => $appearanceConfig->getDesign()->value,
            'numberOfColumns' => $appearanceConfig->getNumberOfColumns(),
            'postPropertiesToDisplay' => $appearanceConfig->getPostPropertiesToDisplay(),
            'taxonomiesToDisplay' => $appearanceConfig->getTaxonomiesToDisplay(),
            'displayFeaturedImage' => $appearanceConfig->shouldDisplayFeaturedImage(),
            'displayReadingTime' => $appearanceConfig->shouldDisplayReadingTime(),
            // Filter settings
            'textSearchEnabled' => $filterConfig->isTextSearchEnabled(),
            'dateFilterEnabled' => $filterConfig->isDateFilterEnabled(),
            // Pagination and ordering
            'postsPerPage' => $getPostsConfig->getPostsPerPage(),
            'orderBy' => $getPostsConfig->getOrderBy(),
            'order' => strtolower($getPostsConfig->getOrder()->value),
        ];
    }
}
