<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WpService\Contracts\GetTerms;

/**
 * Get arguments for taxonomy filters select components
 */
class GetTaxonomyFiltersSelectComponentArguments implements ViewCallableProviderInterface
{
    /**
     * Constructor
     *
     * @param FilterConfigInterface $filterConfig
     * @param GetTerms $wpService
     */
    public function __construct(
        private FilterConfigInterface $filterConfig,
        private GetPostsConfigInterface $getPostsConfig,
        private GetTerms $wpService,
        private string $queryVarNamePrefix
    ) {
    }

    /**
     * Get callable
     */
    public function getCallable(): callable
    {
        return fn() => $this->getSelectComponentArguments();
    }

    /**
     * Get select component arguments for taxonomy filters
     */
    private function getSelectComponentArguments(): array
    {
        $taxonomyFilterConfigs = $this->filterConfig->getTaxonomiesEnabledForFiltering();

        if (empty($taxonomyFilterConfigs)) {
            return [];
        }

        $terms = $this->getTermsForTaxonomies(array_map(static fn(TaxonomyFilterConfigInterface $config) => $config->getTaxonomy()->name, $taxonomyFilterConfigs));

        if (empty($terms)) {
            return [];
        }

        $termsByTaxonomy = $this->groupTermsByTaxonomy($terms);

        return $this->buildSelectArguments($taxonomyFilterConfigs, $termsByTaxonomy);
    }

    /**
     * Get terms for specified taxonomies
     */
    private function getTermsForTaxonomies(array $taxonomyNames): array
    {
        $terms = $this->wpService->getTerms([
            'taxonomy'   => $taxonomyNames,
            'hide_empty' => false,
        ]);
        return is_wp_error($terms) ? [] : $terms;
    }

    /**
     * Group terms by their taxonomy
     */
    private function groupTermsByTaxonomy(array $terms): array
    {
        $grouped = [];
        foreach ($terms as $term) {
            $grouped[$term->taxonomy][] = $term;
        }
        return $grouped;
    }

    /**
     * Build select arguments for each taxonomy
     *
     * @param TaxonomyFilterConfigInterface[] $taxonomyFilterConfigs
     * @param array<string, WP_Term[]> $termsByTaxonomy
     * @return array[]
     */
    private function buildSelectArguments(array $taxonomyFilterConfigs, array $termsByTaxonomy): array
    {
        $allSelectArguments = [];
        foreach ($taxonomyFilterConfigs as $taxonomyConfig) {
            $taxonomyName = $taxonomyConfig->getTaxonomy()->name;

            if (empty($termsByTaxonomy[$taxonomyName])) {
                continue;
            }

            $options         = $this->buildOptions($termsByTaxonomy[$taxonomyName]);
            $selectArguments = [
                'label'       => $taxonomyConfig->getTaxonomy()->label,
                'name'        => $this->queryVarNamePrefix . $taxonomyName,
                'required'    => false,
                'placeholder' => $taxonomyConfig->getTaxonomy()->label,
                'multiple'    => $taxonomyConfig->getFilterType() === TaxonomyFilterType::MULTISELECT ? true : false,
                'options'     => $options,
            ];

            $preselected = $this->getPreselectedTermSlugs($taxonomyName);

            if (!empty($preselected)) {
                $selectArguments['preselected'] = $preselected;
            }

            $allSelectArguments[] = $selectArguments;
        }
        return $allSelectArguments;
    }

    /**
     * Build options array from terms
     */
    private function buildOptions(array $terms): array
    {
        $options = [];
        foreach ($terms as $term) {
            $options[$term->slug] = sprintf('%s (%d)', $term->name, $term->count);
        }
        return $options;
    }

    /**
     * Get preselected term slugs for a taxonomy
     */
    private function getPreselectedTermSlugs(string $taxonomyName): array
    {
        $terms = $this->getPostsConfig->getTerms();
        if (empty($terms)) {
            return [];
        }
        $filtered = array_filter($terms, static fn($term) => $term->taxonomy === $taxonomyName);
        return array_map(static fn($term) => $term->slug, $filtered);
    }
}
