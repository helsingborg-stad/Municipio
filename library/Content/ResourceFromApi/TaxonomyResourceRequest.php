<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Helper\RestRequestHelper;
use Municipio\Helper\WPQueryToRestParamsConverter;
use Municipio\Helper\WPTermQueryToRestParamsConverter;
use stdClass;
use WP_Post;
use WP_Term;

class TaxonomyResourceRequest implements ResourceRequestInterface
{
    public static function getCollection(object $resource, ?array $queryArgs = null): array
    {
        $url = self::getCollectionUrl($resource, $queryArgs);

        if (empty($url)) {
            return [];
        }

        $termsFromApi = RestRequestHelper::getFromApi($url);

        if (is_wp_error($termsFromApi) || !is_array($termsFromApi)) {
            return [];
        }

        return array_map(
            fn ($term) => self::convertRestApiTermToWPTerm((object)$term, $resource),
            $termsFromApi
        );
    }

    public static function getSingle($id, object $resource): ?object
    {
        $url = self::getSingleUrl($id, $resource);

        if (empty($url)) {
            return null;
        }

        $termFromApi = RestRequestHelper::getFromApi($url);

        if (is_wp_error($termFromApi) || !is_array($termFromApi)) {
            return null;
        }

        return self::convertRestApiTermToWPTerm($termFromApi[0], $resource);
    }

    public static function getCollectionHeaders(object $resource, ?array $queryArgs): array
    {
        $url = self::getCollectionUrl($resource, $queryArgs);

        if (empty($url)) {
            return [];
        }

        $headers = RestRequestHelper::getHeadersFromApi($url);

        if (is_wp_error($headers) || !is_array($headers)) {
            return [];
        }

        return $headers;
    }

    public static function getMeta(int $id, string $metaKey, object $resource, bool $single = true)
    {
        $url         = self::getSingleUrl($id, $resource);
        $termFromApi = RestRequestHelper::getFromApi($url);

        if (isset($termFromApi->acf) && isset($termFromApi->acf->$metaKey)) {
            if (is_array($termFromApi->acf->$metaKey)) {
                return [$termFromApi->acf->$metaKey];
            }

            return $termFromApi->acf->$metaKey;
        }

        if (isset($termFromApi->$metaKey)) {
            if (is_array($termFromApi->$metaKey)) {
                return [$termFromApi->$metaKey];
            }

            return $termFromApi->$metaKey;
        }

        return null;
    }

    private static function getSingleUrl($id, object $resource):?string {

        $collectionUrl = self::getCollectionUrl($resource);

        if( is_numeric($id) ) {
            return trailingslashit($collectionUrl) . $id;
        }

        return "{$collectionUrl}/?slug={$id}";
    }

    private static function getCollectionUrl(object $resource, ?array $queryArgs = null): ?string
    {
        $resourceUrl = get_field('taxonomy_source', $resource->resourceID);

        if (empty($resourceUrl)) {
            return null;
        }

        $restParams = !empty($queryArgs)
            ? '?' . WPTermQueryToRestParamsConverter::convertToRestParamsString($queryArgs)
            : '';

        return $resourceUrl . $restParams;
    }

    private static function getLocalID(int $id, object $resource): int
    {
        return -(int)"{$resource->resourceID}{$id}";
    }

    private static function convertRestApiTermToWPTerm(stdClass $termFromApi, object $resource): WP_Term
    {
        $localID = self::getLocalID($termFromApi->id, $resource);
        $localTaxonomy = $resource->name;

        $term                   = new WP_Term(new \stdClass());
        $term->term_id          = $localID;
        $term->name             = $termFromApi->name;
        $term->slug             = $termFromApi->slug;
        $term->term_group       = $termFromApi->term_group ?? 0;
        $term->term_taxonomy_id = $termFromApi->taxonomy_id ?? 0;
        $term->taxonomy         = $localTaxonomy;
        $term->description      = $termFromApi->description ?? '';
        $term->parent           = $termFromApi->parent ?? 0;
        $term->count            = $termFromApi->count ?? 0;
        $term->filter           = 'raw';

        return $term;

        return apply_filters('Municipio/Content/ResourceFromApi/ConvertRestApiTermToWPTerm', $term, $termFromApi, $localTaxonomy);
    }
}
