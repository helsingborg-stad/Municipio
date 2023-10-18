<?php

namespace Municipio\Content;

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
            isset($taxonomyDefinition['api_source_url']) && !empty($taxonomyDefinition['api_source_url'])
        );

        return array_map(fn ($taxonomyDefinition) => sanitize_title($taxonomyDefinition['label']), $taxonomiesFromApi);
    }

    public function addHooks(): void
    {
        add_filter('get_terms', [$this, 'modifyGetTerms'], 10, 4);
        add_filter('get_object_terms', [$this, 'modifyGetObjectTerms'], 10, 4);
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

        return CustomTaxonomyFromApi::getCollection(null, $queryVars['taxonomy'][0]);
    }
}
