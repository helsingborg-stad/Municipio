<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WP_Taxonomy;
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
     * @param array<string, WP_Taxonomy> $wpTaxonomies
     */
    public function __construct(
        private FilterConfigInterface $filterConfig,
        private GetPostsConfigInterface $getPostsConfig,
        private GetTerms $wpService,
        private array $wpTaxonomies,
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
        $taxonomies = $this->filterConfig->getTaxonomiesEnabledForFiltering();
        if (empty($taxonomies)) {
            return [];
        }

        $wpTaxonomies = $this->filterWpTaxonomies($taxonomies);
        if (empty($wpTaxonomies)) {
            return [];
        }

        $terms = $this->getTermsForTaxonomies(array_keys($wpTaxonomies));
        if (empty($terms)) {
            return [];
        }

        $termsByTaxonomy = $this->groupTermsByTaxonomy($terms);

        return $this->buildSelectArguments($wpTaxonomies, $termsByTaxonomy);
    }

    /**
     * Filter WP taxonomies based on provided taxonomy names
     */
    private function filterWpTaxonomies(array $taxonomies): array
    {
        return array_filter(
            $this->wpTaxonomies,
            fn($key) => in_array($key, $taxonomies),
            ARRAY_FILTER_USE_KEY
        );
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
     */
    private function buildSelectArguments(array $wpTaxonomies, array $termsByTaxonomy): array
    {
        $allSelectArguments = [];
        foreach ($wpTaxonomies as $wpTaxonomy) {
            $taxonomyName = $wpTaxonomy->name;
            if (empty($termsByTaxonomy[$taxonomyName])) {
                continue;
            }

            $options         = $this->buildOptions($termsByTaxonomy[$taxonomyName]);
            $selectArguments = [
                'label'       => $wpTaxonomy->label,
                'name'        => $this->queryVarNamePrefix . $taxonomyName,
                'required'    => false,
                'placeholder' => $wpTaxonomy->label,
                'multiple'    => true,
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
        $filtered = array_filter($terms, fn($term) => $term->taxonomy === $taxonomyName);
        return array_map(fn($term) => $term->slug, $filtered);
    }
}
