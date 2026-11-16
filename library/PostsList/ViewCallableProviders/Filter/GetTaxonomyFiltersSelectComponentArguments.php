<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use Municipio\PostsList\Config\GetPostsConfig\GetPostsConfigInterface;
use Municipio\PostsList\ViewCallableProviders\Filter\TermPostCounts\TermPostCountsInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetTerms;
use WP_Term;

/**
 * Get arguments for taxonomy filters select components.
 *
 * Taxonomies using the checkbox filter type get a `checkboxes` key holding the arguments for one
 * option component per term, instead of the select options.
 *
 * When term post counts are provided, each term shows how many posts in the list it matches
 * (e.g. only upcoming events), and terms matching no posts are left out unless selected.
 * Without them, the total post count stored on the term is shown.
 */
class GetTaxonomyFiltersSelectComponentArguments implements ViewCallableProviderInterface
{
    /**
     * Constructor
     *
     * @param FilterConfigInterface $filterConfig
     * @param GetTerms&ApplyFilters $wpService
     * @param TermPostCountsInterface|null $termPostCounts
     */
    public function __construct(
        private FilterConfigInterface $filterConfig,
        private GetPostsConfigInterface $getPostsConfig,
        private GetTerms&ApplyFilters $wpService,
        private string $queryVarNamePrefix,
        private ?TermPostCountsInterface $termPostCounts = null,
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
            $preselected  = $this->getPreselectedTermSlugs($taxonomyName);
            $terms        = $this->applyTermPostCounts($termsByTaxonomy[$taxonomyName] ?? [], $taxonomyName, $preselected);

            if (empty($terms)) {
                continue;
            }

            $label = $this->wpService->applyFilters('Municipio/Archive/TaxonomyFilter/Label', $taxonomyConfig->getTaxonomy()->label, $taxonomyConfig->getTaxonomy());
            $name  = $this->queryVarNamePrefix . $taxonomyName;

            if ($taxonomyConfig->getFilterType() === TaxonomyFilterType::CHECKBOX) {
                $allSelectArguments[] = [
                    'label'      => $label,
                    'checkboxes' => $this->buildCheckboxes($terms, $name, $preselected),
                ];
                continue;
            }

            $options         = $this->buildOptions($terms);
            $selectArguments = [
                'label'       => $label,
                'name'        => $name,
                'required'    => false,
                'placeholder' => $this->wpService->applyFilters('Municipio/Archive/TaxonomyFilter/Placeholder', $taxonomyConfig->getTaxonomy()->label, $taxonomyConfig->getTaxonomy()),
                'multiple'    => $taxonomyConfig->getFilterType() === TaxonomyFilterType::MULTISELECT ? true : false,
                'options'     => $options,
            ];

            if (!empty($preselected)) {
                $selectArguments['preselected'] = $preselected;
            }

            $allSelectArguments[] = $selectArguments;
        }
        return $allSelectArguments;
    }

    /**
     * Replace the stored term counts with the number of posts in the list each term matches.
     *
     * Terms matching no posts are left out, unless they are selected, so that the selection can
     * still be seen and removed.
     *
     * @param WP_Term[] $terms
     * @param string $taxonomyName
     * @param string[] $preselected Slugs of the terms currently filtered on.
     * @return WP_Term[]
     */
    private function applyTermPostCounts(array $terms, string $taxonomyName, array $preselected): array
    {
        if ($this->termPostCounts === null) {
            return $terms;
        }

        $counts = $this->termPostCounts->getCounts($taxonomyName);
        $result = [];

        foreach ($terms as $term) {
            $count = $counts[$term->term_id] ?? 0;

            if ($count === 0 && !in_array($term->slug, $preselected, true)) {
                continue;
            }

            $termWithCount        = clone $term;
            $termWithCount->count = $count;
            $result[]             = $termWithCount;
        }

        return $result;
    }

    /**
     * Build options array from terms
     */
    private function buildOptions(array $terms): array
    {
        $options = [];
        foreach ($terms as $term) {
            $options[$term->slug] = $this->formatTermLabel($term);
        }
        return $options;
    }

    /**
     * Build option component arguments, one checkbox per term.
     *
     * @param WP_Term[] $terms
     * @param string $name Query var name for the taxonomy.
     * @param string[] $preselected Slugs of the terms currently filtered on.
     * @return array[]
     */
    private function buildCheckboxes(array $terms, string $name, array $preselected): array
    {
        return array_map(fn($term) => [
            'type'    => 'checkbox',
            'label'   => $this->formatTermLabel($term),
            'name'    => $name . '[]',
            'value'   => $term->slug,
            'checked' => in_array($term->slug, $preselected, true),
        ], array_values($terms));
    }

    /**
     * Format the label shown for a term, including its post count.
     */
    private function formatTermLabel(WP_Term $term): string
    {
        return sprintf('%s (%d)', $term->name, $term->count);
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
