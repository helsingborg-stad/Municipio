<?php

namespace Municipio\PostsList\Config\GetPostsConfig\GetTermsFromGetParams;

use Municipio\PostsList\Config\FilterConfig\FilterConfigInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfigInterface;
use WpService\Contracts\GetTerms;

/**
 * Class to extract terms from GET parameters
 */
class GetTermsFromGetParams
{
    /**
     * Constructor
     */
    public function __construct(
        private array $getParams,
        private FilterConfigInterface $filterConfig,
        private string $prefix,
        private GetTerms $wpService
    ) {
    }

    /**
     * Get the terms from the query parameters.
     *
     * @return \WP_Term[]
     */
    public function getTerms(): array
    {
        if (empty($this->getParams) || !is_array($this->getParams)) {
            return [];
        }

        $taxonomiesFromParams = $this->getTaxonomiesFromParams(...$this->filterConfig->getTaxonomiesEnabledForFiltering());

        if (empty($taxonomiesFromParams)) {
            return [];
        }

        $terms = [];
        foreach ($taxonomiesFromParams as $taxonomyKey) {
            $termSlugs = $this->getTermSlugsForTaxonomy($taxonomyKey);
            if (empty($termSlugs)) {
                continue;
            }

            $taxonomy   = $this->cleanTaxonomyKey($taxonomyKey);
            $foundTerms = $this->wpService->getTerms([
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
                'slug'       => $termSlugs,
            ]);

            if (is_array($foundTerms) && !empty($foundTerms)) {
                $terms = array_merge($terms, $foundTerms);
                continue;
            }

            // None of the requested terms exist (e.g. an empty term that has been cleaned up).
            // Keep the filter active so the query yields no posts instead of silently dropping it.
            $terms[] = $this->createUnmatchedTerm($taxonomy, (string) reset($termSlugs));
        }

        return $terms;
    }

    /**
     * Create a placeholder term representing a requested term that does not exist.
     *
     * The term id 0 never matches any post, so a tax query using it returns no results.
     *
     * @param string $taxonomy
     * @param string $slug
     * @return \WP_Term
     */
    private function createUnmatchedTerm(string $taxonomy, string $slug): \WP_Term
    {
        $term           = new \WP_Term((object) []);
        $term->term_id  = 0;
        $term->taxonomy = $taxonomy;
        $term->slug     = $slug;

        return $term;
    }

    /**
     * Extract and clean taxonomy keys from GET params.
     *
     * @param TaxonomyFilterConfigInterface[] $enabledTaxonomies
     * @return array
     */
    private function getTaxonomiesFromParams(TaxonomyFilterConfigInterface ...$enabledTaxonomies): array
    {
        return array_filter(
            array_keys($this->getParams),
            fn($key) => in_array(
                $this->cleanTaxonomyKey($key),
                array_map(fn(TaxonomyFilterConfigInterface $config) => $config->getTaxonomy()->name, $enabledTaxonomies)
            )
        );
    }

    /**
     * Clean taxonomy key by removing prefix and array notation.
     *
     * @param string $key
     * @return string
     */
    private function cleanTaxonomyKey(string $key): string
    {
        $cleanKey = rtrim(urldecode($key), '[]');
        return str_replace($this->prefix, '', $cleanKey);
    }

    /**
     * Get term slugs for a given taxonomy key.
     *
     * @param string $taxonomyKey
     * @return array
     */
    private function getTermSlugsForTaxonomy(string $taxonomyKey): array
    {
        if (!isset($this->getParams[$taxonomyKey])) {
            return [];
        }
        return array_values(array_filter(
            (array) $this->getParams[$taxonomyKey],
            static fn($slug) => is_scalar($slug) && (string) $slug !== ''
        ));
    }
}
