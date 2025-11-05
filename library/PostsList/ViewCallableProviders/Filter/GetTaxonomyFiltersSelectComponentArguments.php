<?php

namespace Municipio\PostsList\ViewCallableProviders\Filter;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\ViewCallableProviders\ViewCallableProviderInterface;
use WpService\Contracts\GetTerms;

/**
 * Get arguments for taxonomy filters select components
 */
class GetTaxonomyFiltersSelectComponentArguments implements ViewCallableProviderInterface
{
    public function __construct(
        private FilterConfigInterface $filterConfig,
        private GetTerms $wpService
    ) {
    }

    public function getCallable(): callable
    {
        return fn() => $this->getSelectComponentArguments();
    }

    private function getSelectComponentArguments(): array
    {
        $selectArguments = [];
        $taxonomies      = $this->filterConfig->getTaxonomiesEnabledForFiltering();

        if (empty($taxonomies)) {
            return $selectArguments;
        }

        // Get all terms for all taxonomies in one call
        $terms = $this->wpService->getTerms([
            'taxonomy'   => $taxonomies,
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            return $selectArguments;
        }

        // Group terms by taxonomy
        $termsByTaxonomy = [];
        foreach ($terms as $term) {
            $termsByTaxonomy[$term->taxonomy][] = $term;
        }

        foreach ($taxonomies as $taxonomy) {
            if (empty($termsByTaxonomy[$taxonomy])) {
                continue;
            }

            $options = [];
            foreach ($termsByTaxonomy[$taxonomy] as $term) {
                $options[$term->slug] = sprintf('%s (%d)', $term->name, $term->count);
            }

            $selectArguments[] = [
                'label'       => ucfirst($taxonomy),
                'name'        => 'filter-' . $taxonomy,
                'required'    => false,
                'placeholder' => ucfirst($taxonomy),
                'preselected' => false,
                'multiple'    => true,
                'options'     => $options,
            ];
        }

        return $selectArguments;
    }
}
