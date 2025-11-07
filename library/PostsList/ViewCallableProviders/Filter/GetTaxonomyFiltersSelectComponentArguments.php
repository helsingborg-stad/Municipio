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
        private array $wpTaxonomies
    ) {
    }

    public function getCallable(): callable
    {
        return fn() => $this->getSelectComponentArguments();
    }

    private function getSelectComponentArguments(): array
    {
        $allSelectArguments = [];
        $taxonomies         = $this->filterConfig->getTaxonomiesEnabledForFiltering();

        if (empty($taxonomies)) {
            return $allSelectArguments;
        }

        $wpTaxonomies = array_filter($this->wpTaxonomies, fn($key) => in_array($key, $taxonomies), ARRAY_FILTER_USE_KEY);

        if (empty($wpTaxonomies)) {
            return $allSelectArguments;
        }

        // Get all terms for all taxonomies in one call
        $terms = $this->wpService->getTerms([
            'taxonomy'   => array_keys($wpTaxonomies),
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms) || empty($terms)) {
            return $allSelectArguments;
        }

        // Group terms by taxonomy
        $termsByTaxonomy = [];
        foreach ($terms as $term) {
            $termsByTaxonomy[$term->taxonomy][] = $term;
        }

        foreach ($wpTaxonomies as $wpTaxonomy) {
            if (empty($termsByTaxonomy[$wpTaxonomy->name])) {
                continue;
            }

            $options = [];
            foreach ($termsByTaxonomy[$wpTaxonomy->name] as $term) {
                $options[$term->slug] = sprintf('%s (%d)', $term->name, $term->count);
            }

            $selectArguments = [
                'label'       => $wpTaxonomy->label,
                'name'        => $wpTaxonomy->name,
                'required'    => false,
                'placeholder' => $wpTaxonomy->label,
                'multiple'    => true,
                'options'     => $options,
            ];

            if ($this->getPostsConfig->getTerms()) {
                $preselectedTerms = array_filter(
                    $this->getPostsConfig->getTerms(),
                    fn($term) => $term->taxonomy === $wpTaxonomy->name
                );
                $termSlugs        = array_map(
                    fn($term) => $term->slug,
                    $preselectedTerms
                );

                if (!empty($termSlugs)) {
                    $selectArguments['preselected'] = $termSlugs;
                }
            }

            $allSelectArguments[] = $selectArguments;
        }

        return $allSelectArguments;
    }
}
