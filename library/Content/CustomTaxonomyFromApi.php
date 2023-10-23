<?php

namespace Municipio\Content;

use Municipio\Helper\RestRequestHelper;
use WP_Term;
use WP_Term_Query;

class CustomTaxonomyFromApi {

    private static ?array $taxonomies = null;

    private static function getCollectionUrl(string $taxonomy):?string {
        
        if (self::$taxonomies === null) {
            self::setupTaxonomies();
        }

        if (isset(self::$taxonomies[$taxonomy])) {
            return self::$taxonomies[$taxonomy];
        }

        return null;
    }

    private static function setupTaxonomies(): void {
        
        $taxonomyDefinitions = CustomTaxonomy::getTypeDefinitions();

        $taxonomiesFromApi = array_filter(
            $taxonomyDefinitions,
            fn ($taxonomyDefinition) =>
            isset($taxonomyDefinition['api_source_url']) && !empty($taxonomyDefinition['api_source_url'])
        );

        foreach ($taxonomiesFromApi as $taxonomy) {
            self::$taxonomies[sanitize_title($taxonomy['label'])] = $taxonomy['api_source_url'];
        }
    }

    public static function getSingle($id, string $taxonomy): ?WP_Term
    {
        $url = self::getSingleUrl($id, $taxonomy);

        if( empty($url) ) {
            return null;
        }

        $termFromApi = RestRequestHelper::getFromApi($url);

        if (is_wp_error($termFromApi)) {
            return new WP_Term(new \stdClass());
        }

        return self::convertRestApiTermToWPTerm($termFromApi, $taxonomy);
    }

    public static function getCollection(?WP_Term_Query $termQuery, $taxonomy): array
    {
        $url = self::getCollectionUrl($taxonomy);

        if( empty($url) ) {
            return [];
        }

        $url .= $termQuery ? '?' . self::convertWpTermQueryToWPRestAPIQueryString($termQuery) : '';
        $termsFromApi = RestRequestHelper::getFromApi($url);

        if (is_wp_error($termsFromApi) || !is_array($termsFromApi)) {
            return [];
        }

        return array_map(fn($termFromApi) => self::convertRestApiTermToWPTerm($termFromApi, $taxonomy), $termsFromApi);
    }

    private static function getSingleUrl($id, string $taxonomy): ?string
    {
        $url = self::getCollectionUrl($taxonomy);

        if (empty($url)) {
            return null;
        }

        return "{$url}/{$id}";
    }

    private static function convertWpTermQueryToWPRestAPIQueryString(WP_Term_Query $termQuery): string
    {
        $rest_query = [];

        // Loop through all query vars and map them to REST API parameters
        foreach ($termQuery->query_vars as $key => $value) {
            
            if (empty($value)) {
                continue;
            }
    
            switch ($key) {
                case 'object_ids':
                    if (is_array($value)) {
                        $rest_query['post'] = implode(',', array_filter($value, 'is_numeric'));
                    } else if (is_numeric($value)) {
                        $rest_query['post'] = $value;
                    }
                    break;
                case 'taxonomy':
                    if (is_array($value)) {
                        $rest_query['taxonomy'] = implode(',', $value);
                    } else if (is_string($value)) {
                        $rest_query['taxonomy'] = $value;
                    }
                    break;
                case 'order':
                    if (in_array(strtolower($value), ['asc', 'desc'])) {
                        $rest_query['order'] = strtolower($value);
                    }
                    break;
                case 'orderby':
                    if (is_string($value)) {
                        $rest_query['orderby'] = $value;
                    }
                    break;
                case 'hide_empty':
                    if (is_bool($value)) {
                        $rest_query['hide_empty'] = $value ? 'true' : 'false';
                    }
                    break;
                case 'include':
                case 'exclude':
                case 'parent':
                case 'parent_exclude':
                    if (is_array($value)) {
                        $rest_query[$key] = implode(',', array_filter($value, 'is_numeric'));
                    } else if (is_numeric($value)) {
                        $rest_query[$key] = $value;
                    }
                    break;
                case 'slug':
                    if (is_string($value)) {
                        $rest_query['slug'] = $value;
                    }
                    break;
                case 'offset':
                case 'number':
                    if (is_numeric($value)) {
                        $rest_query[$key] = $value;
                    }
                    break;
                case 'search':
                    if (is_string($value)) {
                        $rest_query['search'] = $value;
                    }
                    break;
                // Add more cases as needed...
            }
        }

        return implode('&', array_map(
            function ($key, $value) { return sprintf('%s=%s', urlencode($key), urlencode($value)); },
            array_keys($rest_query),
            $rest_query
        ));
    }
    
    private static function convertRestApiTermToWPTerm(object $termFromApi, string $taxonomyName): WP_Term
    {
        $term                   = new WP_Term(new \stdClass());
        $term->term_id          = $termFromApi->id;
        $term->name             = $termFromApi->name;
        $term->slug             = $termFromApi->slug;
        $term->term_group       = $termFromApi->term_group ?? 0;
        $term->term_taxonomy_id = $termFromApi->taxonomy_id ?? 0;
        $term->taxonomy         = $taxonomyName;
        $term->description      = $termFromApi->description ?? '';
        $term->parent           = $termFromApi->parent ?? 0;
        $term->count            = $termFromApi->count ?? 0;
        $term->filter           = 'raw';

        return $term;
    }
}