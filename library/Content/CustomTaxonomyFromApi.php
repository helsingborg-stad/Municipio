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
        
        if (!function_exists('get_field')) {
            return;
        }

        $taxonomyDefinitions = get_field('avabile_dynamic_taxonomies', 'option');
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

        $url .= $termQuery ? self::convertWpTermQueryToWPRestAPIQueryString($termQuery) : '';
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
        $queryString = '?';

        if ($termQuery->query_vars['object_ids']) {
            $queryString .= 'post=' . implode(',', $termQuery->query_vars['object_ids']) . '&';
        }

        return rtrim($queryString, '&');
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