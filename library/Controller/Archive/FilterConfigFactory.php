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
    public function __construct(private array $data, private GetTaxonomies $wpService)
    {
    }

    /**
     * Create a FilterConfig instance
     *
     * @return FilterConfigInterface
     */
    public function create(): FilterConfigInterface
    {
        $isEnabled             = $this->showFilter();
        $resetUrl              = $this->getPostTypeArchiveLink($this->getPostType()) ?? '';
        $isDateFilterEnabled   = $this->enableDateFilter();
        $isTextSearchEnabled   = $this->enableTextSearch();
        $taxonomyFilterConfigs = $this->getFilterTaxonomiesFromSettings();

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
     * @return boolean
     */
    private function showFilter(): bool
    {
        $enabledFilters = false;

        if (!is_object($this->data['archiveProps'])) {
            $this->data['archiveProps'] = (object) [];
        }

        $arrayWithoutEmptyValues = isset($this->data['archiveProps']->enabledFilters)
            ? array_filter($this->data['archiveProps']->enabledFilters, fn($element) => !empty($element))
            : [];

        if (!empty($arrayWithoutEmptyValues)) {
            $enabledFilters = $this->data['archiveProps']->enabledFilters;
        }

        $enabledFilters = apply_filters('Municipio/Archive/showFilter', $enabledFilters, $this->data['archiveProps']);

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
    private function getPostType(): string
    {
        return !empty($this->data['postType']) ? $this->data['postType'] : 'page';
    }

    /**
     * Boolean function to determine if date filter should be enabled
     *
     * @return  boolean                 True or false val.
     */
    private function enableDateFilter()
    {
        if (!is_object($this->data['archiveProps'])) {
            $this->data['archiveProps'] = (object) [];
        }

        return (bool) in_array(
            'date_range',
            isset($this->data['archiveProps']->enabledFilters) && is_array($this->data['archiveProps']->enabledFilters)
                ? $this->data['archiveProps']->enabledFilters
                : []
        );
    }

    /**
     * Boolean function to determine if text search should be enabled
     *
     * @return  boolean                 True or false val.
     */
    private function enableTextSearch()
    {
        if (!is_object($this->data['archiveProps'])) {
            $this->data['archiveProps'] = (object) [];
        }

        return (bool) in_array(
            'text_search',
            isset($this->data['archiveProps']->enabledFilters) && is_array($this->data['archiveProps']->enabledFilters)
                ? $this->data['archiveProps']->enabledFilters
                : []
        );
    }

    /**
     * Get taxonomies to use for filtering from settings
     *
     * @return array
     */
    private function getFilterTaxonomiesFromSettings(): array
    {
        $taxonomies = apply_filters('Municipio/Archive/getTaxonomyFilters/taxonomies', array_diff(
            $this->data['archiveProps']->enabledFilters ?? [],
            [$this->currentTaxonomy()]
        ), $this->currentTaxonomy());

        if (empty($taxonomies)) {
            return [];
        }

        // Wash out invalid taxonomies
        $allTaxonomies = $this->wpService->getTaxonomies([], 'names');
        $taxonomies    = array_values(array_intersect($allTaxonomies, $taxonomies));
        return array_map(function ($taxonomyName) {
            $camelCasedName = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $taxonomyName))));
            $filterType     = isset($this->data['archiveProps']->{$camelCasedName . 'FilterFieldType'}) && $this->data['archiveProps']->{$camelCasedName . 'FilterFieldType'} === 'multi'
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
