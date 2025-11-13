<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\Config\FilterConfig\DefaultFilterConfig;
use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use WpService\Contracts\GetTaxonomies;

/**
 * Factory class for creating FilterConfig instances
 */
class FilterConfigFactory
{
    /**
     * Constructor
     */
    public function __construct(private GetTaxonomies $wpService)
    {
    }

    /**
     * Create a FilterConfig instance
     *
     * @param array $data
     * @return FilterConfigInterface
     */
    public function create(array $data): FilterConfigInterface
    {
        $isEnabled             = $this->showFilter($data['archiveProps']);
        $resetUrl              = $this->getPostTypeArchiveLink($this->getPostType($data));
        $isDateFilterEnabled   = $this->enableDateFilter($data['archiveProps']);
        $isTextSearchEnabled   = $this->enableTextSearch($data['archiveProps']);
        $taxonomyFilterConfigs = $this->getFilterTaxonomiesFromSettings((array) $data['archiveProps']);

        return new class ($isEnabled, $resetUrl, $isDateFilterEnabled, $isTextSearchEnabled, $taxonomyFilterConfigs) extends DefaultFilterConfig {
            /**
             * Constructor
             */
            public function __construct(
                private bool $isEnabled,
                private string $resetUrl,
                private bool $isDateFilterEnabled,
                private bool $isTextSearchEnabled,
                private array $taxonomyFilterConfigs
            ) {
            }

            /**
             * @inheritDoc
             */
            public function isEnabled(): bool
            {
                return $this->isEnabled;
            }

            /**
             * @inheritDoc
             */
            public function isTextSearchEnabled(): bool
            {
                return $this->isTextSearchEnabled;
            }

            /**
             * @inheritDoc
             */
            public function getResetUrl(): ?string
            {
                return $this->resetUrl;
            }

            /**
             * @inheritDoc
             */
            public function isDateFilterEnabled(): bool
            {
                return $this->isDateFilterEnabled;
            }

            /**
             * @inheritDoc
             */
            public function getTaxonomiesEnabledForFiltering(): array
            {
                return $this->taxonomyFilterConfigs;
            }
        };
    }

    /**
     * Determines if the filter should be shown.
     *
     * @param object $args
     * @return boolean
     */
    private function showFilter($args): bool
    {
        $enabledFilters = false;

        if (!is_object($args)) {
            $args = (object) [];
        }

        $arrayWithoutEmptyValues = isset($args->enabledFilters)
            ? array_filter($args->enabledFilters, fn($element) => !empty($element))
            : [];

        if (!empty($arrayWithoutEmptyValues)) {
            $enabledFilters = $args->enabledFilters;
        }

        $enabledFilters = apply_filters('Municipio/Archive/showFilter', $enabledFilters, $args);

        return is_array($enabledFilters) && !empty($enabledFilters) ? true : (bool) $enabledFilters;
    }

    /**
     * Get the link to this page, without any query parameters
     *
     * @param   string  $postType   The current post type
     *
     * @return string
     */
    public function getPostTypeArchiveLink($postType)
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $realPath      = (string) parse_url(home_url() . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $postTypePath  = (string) parse_url(get_post_type_archive_link($postType), PHP_URL_PATH);
            $mayBeTaxonomy = (bool)   ($realPath != $postTypePath);

            if ($mayBeTaxonomy && is_a(get_queried_object(), 'WP_Term')) {
                return get_term_link(get_queried_object());
            }
        }

        return get_post_type_archive_link($postType);
    }

    /**
     * Get the post type for the archive
     *
     * @return string
     */
    private function getPostType(array $data): string
    {
        return !empty($data['postType']) ? $data['postType'] : 'page';
    }

    /**
     * Boolean function to determine if date filter should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    private function enableDateFilter($args)
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        return (bool) in_array(
            'date_range',
            isset($args->enabledFilters) && is_array($args->enabledFilters) ? $args->enabledFilters : []
        );
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @param   string      $postType   The current post type
     * @return  boolean                 True or false val.
     */
    private function enableTextSearch($args)
    {
        if (!is_object($args)) {
            $args = (object) [];
        }

        return (bool) in_array(
            'text_search',
            isset($args->enabledFilters) && is_array($args->enabledFilters) ? $args->enabledFilters : []
        );
    }

    /**
     * Get taxonomies to use for filtering from settings
     *
     * @param array $settings
     * @return array
     */
    private function getFilterTaxonomiesFromSettings(array $settings): array
    {
        $taxonomies = apply_filters('Municipio/Archive/getTaxonomyFilters/taxonomies', array_diff(
            $settings['enabledFilters'] ?? [],
            [$this->currentTaxonomy()]
        ), $this->currentTaxonomy());

        if (empty($taxonomies)) {
            return [];
        }

        // Wash out invalid taxonomies
        $allTaxonomies = $this->wpService->getTaxonomies([], 'names');
        $taxonomies    = array_values(array_intersect($allTaxonomies, $taxonomies));
        return array_map(function ($taxonomyName) use ($settings) {
            $camelCasedName = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $taxonomyName))));
            $filterType     = isset($settings[$camelCasedName . 'FilterFieldType']) && $settings[$camelCasedName . 'FilterFieldType'] === 'multi'
                ? TaxonomyFilterType::MULTISELECT
                : TaxonomyFilterType::SINGLESELECT;
            return new TaxonomyFilterConfig($taxonomyName, $filterType);
        }, $taxonomies);
    }

    /**
     * Get the current taxonomy page
     */
    private function currentTaxonomy()
    {
        $queriedObject = get_queried_object();
        $isTaxArchive  = false;
        if (!empty($queriedObject->taxonomy) && isset($_SERVER['REQUEST_URI'])) {
            $pathParts   = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
            $trimmedPath = end($pathParts);
            if ($queriedObject->slug == $trimmedPath) {
                $isTaxArchive = $queriedObject->taxonomy;
            }
        }
        return $isTaxArchive;
    }
}
