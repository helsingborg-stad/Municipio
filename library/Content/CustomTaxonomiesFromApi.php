<?php

namespace Municipio\Content;

use WP_Taxonomy;
use WP_Term;
use WP_Term_Query;

class CustomTaxonomiesFromApi
{
    private array $taxonomiesFromApi = [];

    public function __construct()
    {
        add_action('init', function(){
            $this->taxonomiesFromApi = $this->getTaxonomiesFromApi();
        }, 10);
    }

    private function getTaxonomiesFromApi(): array
    {
        $taxonomyDefinitions = CustomTaxonomy::getTypeDefinitions();

        $taxonomiesFromApi = array_filter(
            $taxonomyDefinitions,
            fn ($taxonomyDefinition) =>
                isset($taxonomyDefinition['api_source_url']) &&
                !empty($taxonomyDefinition['api_source_url'] ) &&
                filter_var($taxonomyDefinition['api_source_url'], FILTER_VALIDATE_URL) !== false
        );

        return array_map(fn ($taxonomyDefinition) => sanitize_title($taxonomyDefinition['label']), $taxonomiesFromApi);
    }

    public function addHooks(): void
    {
        add_action('pre_get_terms', [$this, 'modifyPreGetTerms'], 10, 1);
        add_filter('get_terms', [$this, 'modifyGetTerms'], 10, 4);
        add_filter('get_object_terms', [$this, 'modifyGetObjectTerms'], 10, 4);
        add_filter('Municipio/Archive/getTaxonomyFilters/option/value', [$this, 'modifyGetTaxonomyFiltersOptionValue'], 10, 3);
    }

    public function modifyGetTaxonomyFiltersOptionValue(string $value, WP_Term $option, WP_Taxonomy $taxonomy): string
    {
        if (!is_a($option, 'WP_Term') || !in_array($taxonomy->name, $this->taxonomiesFromApi)) {
            return $value;
        }

        return $option->term_id;
    }

    public function modifyPreGetTerms(WP_Term_Query $termQuery) {
        
        // If querying a taxonomy that is not from the API, return early
        if (!isset($termQuery->query_vars['taxonomy']) || !in_array($termQuery->query_vars['taxonomy'][0], $this->taxonomiesFromApi)) {
            return;
        }

        // Set suppress filters to false
        $termQuery->query_vars['suppress_filters'] = false;
    }

    public function modifyGetTerms(array $terms, $taxonomy, array $queryVars, WP_Term_Query $termQuery): array
    {
        if (!isset($queryVars['taxonomy']) || !in_array($queryVars['taxonomy'][0], $this->taxonomiesFromApi)) {
            return $terms;
        }

        return CustomTaxonomyFromApi::getCollection($termQuery, $queryVars['taxonomy'][0]);
    }

    public function modifyGetObjectTerms(array $terms, array $objectIds, array $taxonomies, array $queryVars): array
    {
        if (!isset($queryVars['taxonomy']) || !in_array($queryVars['taxonomy'][0], $this->taxonomiesFromApi)) {
            return $terms;
        }

        $termQuery = new WP_Term_Query($queryVars);

        return CustomTaxonomyFromApi::getCollection($termQuery, $queryVars['taxonomy'][0]);
    }
}
