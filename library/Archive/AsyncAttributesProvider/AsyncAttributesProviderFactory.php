<?php

declare(strict_types=1);

namespace Municipio\Archive\AsyncAttributesProvider;

use Municipio\Controller\Archive\AppearanceConfigFactory;
use Municipio\Controller\Archive\ArchiveDefaults;
use Municipio\Controller\Archive\FilterConfigFactory;
use Municipio\Controller\Archive\GetPostsConfigFactory;
use Municipio\PostsList\QueryVars\QueryVars;
use WpService\Contracts\GetTerms;
use WpService\Contracts\GetThemeMod;

/**
 * Factory for creating AsyncAttributesProvider instances
 *
 * Encapsulates the complex initialization of async attributes providers
 * by managing factory dependencies and configuration.
 */
class AsyncAttributesProviderFactory
{
    /**
     * Constructor
     *
     * @param AppearanceConfigFactory $appearanceConfigFactory Factory for appearance config
     */
    public function __construct(
        private AppearanceConfigFactory $appearanceConfigFactory
    ) {
    }

    /**
     * Create async attributes provider for archive
     *
     * @param string $postType The post type of the archive
     * @param object $archiveProps Archive properties from customizer
     * @param GetThemeMod&GetTerms $wpService WordPress service
     * @param array $wpTaxonomies Array of WP_Taxonomy objects
     * @return AsyncAttributesProviderInterface
     */
    public function createForArchive(
        string $postType,
        object $archiveProps,
        GetThemeMod&GetTerms $wpService,
        array $wpTaxonomies,
    ): AsyncAttributesProviderInterface {
        // Build data array for factories
        $data = [
            'archiveProps' => $archiveProps,
            'postType' => $postType,
            'wpService' => $wpService,
        ];

        // Create query vars handler with archive prefix
        $queryVars = new QueryVars(ArchiveDefaults::QUERY_VARS_PREFIX);

        // Create filter config factory (needs taxonomies and query vars)
        $filterConfigFactory = new FilterConfigFactory(
            $data,
            $wpTaxonomies,
            $wpService,
            $queryVars
        );

        // Create posts config factory (needs filter config and query vars)
        $postsConfigFactory = new GetPostsConfigFactory(
            $data,
            $filterConfigFactory->create(),
            $queryVars,
            $wpService
        );

        // Create and return the provider with all dependencies
        return new ArchiveAsyncAttributesProvider(
            $postType,
            $archiveProps,
            $wpService,
            $this->appearanceConfigFactory,
            $filterConfigFactory,
            $postsConfigFactory
        );
    }
}
