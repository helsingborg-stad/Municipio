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

            if (is_array($foundTerms)) {
                $terms = array_merge($terms, $foundTerms);
            }
        }

        return $terms;
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
                array_map(fn(TaxonomyFilterConfigInterface $config) => $config->getTaxonomyName(), $enabledTaxonomies)
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
        return (array) $this->getParams[$taxonomyKey];
    }
}
