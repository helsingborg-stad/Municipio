<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Helper\ResourceFromApiHelper;
use Municipio\Helper\RestRequestHelper;
use Municipio\Helper\WPTermQueryToRestParamsConverter;
use stdClass;
use WP_Term;

/**
 * Class TaxonomyResource
 * Represents a resource obtained from an API.
 */
class TaxonomyResource extends Resource
{
    /**
     * Returns the type of the resource.
     *
     * @return string The type of the resource.
     */
    public function getType(): string
    {
        return ResourceType::TAXONOMY;
    }

    /**
     * Retrieves a collection of taxonomy terms from an API.
     *
     * @param array|null $queryArgs Optional query arguments for filtering the collection.
     * @return array The collection of taxonomy terms.
     */
    public function getCollection(?array $queryArgs = null): array
    {
        $url = $this->getCollectionUrl($queryArgs);

        if (empty($url)) {
            return [];
        }

        $termsFromApi = RestRequestHelper::get($url);

        if (is_wp_error($termsFromApi) || !is_array($termsFromApi)) {
            return [];
        }

        return array_map(
            fn ($term) => $this->convertRestApiTermToWPTerm((object)$term),
            $termsFromApi
        );
    }

    /**
     * Retrieves the headers of the collection from the API.
     *
     * @param array|null $queryArgs Optional query arguments for filtering the collection.
     * @return array The headers of the collection.
     */
    public function getCollectionHeaders(?array $queryArgs = null): array
    {
        $url = $this->getCollectionUrl($queryArgs);

        if (empty($url)) {
            return [];
        }

        $headers = RestRequestHelper::getHeaders($url);

        if (is_wp_error($headers) || !is_array($headers)) {
            return [];
        }

        return $headers;
    }

    /**
     * Retrieves a single taxonomy term from the API based on its ID.
     *
     * @param int $id The ID of the taxonomy term.
     * @return object|null The converted WordPress term object, or null if the term is not found or an error occurs.
     */
    public function getSingle($id): ?object
    {
        $url = $this->getSingleUrl($id);

        if (empty($url)) {
            return null;
        }

        $termFromApi = RestRequestHelper::get($url);

        if (is_wp_error($termFromApi) || !is_array($termFromApi)) {
            return null;
        }

        return $this->convertRestApiTermToWPTerm($termFromApi[0]);
    }

    /**
     * Retrieves the meta value of a taxonomy term from an API.
     *
     * @param int    $id       The ID of the taxonomy term.
     * @param string $metaKey  The meta key of the desired meta value.
     * @param bool   $single   Optional. Whether to return a single value or an array of values. Default is true.
     *
     * @return mixed|null      The meta value of the taxonomy term, or null if not found.
     */
    public function getMeta(int $id, string $metaKey, bool $single = true)
    {
        $url         = $this->getSingleUrl($id);
        $termFromApi = RestRequestHelper::get($url);

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

    /**
     * Returns the collection URL with optional query arguments.
     *
     * @param array|null $queryArgs Optional query arguments.
     * @return string|null The collection URL with query arguments.
     */
    private function getCollectionUrl(?array $queryArgs = null): ?string
    {
        $url = $this->getBaseUrl();

        $restParams = !empty($queryArgs)
            ? '?' . WPTermQueryToRestParamsConverter::convertToRestParamsString($queryArgs)
            : '';

        return $url . $restParams;
    }

    /**
     * Returns the URL for a single resource based on its ID or slug.
     *
     * @param mixed $id The ID or slug of the resource.
     * @return string|null The URL of the single resource, or null if the ID is not numeric.
     */
    private function getSingleUrl($id): ?string
    {
        $collectionUrl = $this->getCollectionUrl();

        if (is_numeric($id)) {
            return trailingslashit($collectionUrl) . $id;
        }

        return "{$collectionUrl}/?slug={$id}";
    }

    /**
     * Converts a term object from the REST API to a WP_Term object.
     *
     * @param stdClass $termFromApi The term object from the REST API.
     * @return WP_Term The converted WP_Term object.
     */
    private function convertRestApiTermToWPTerm(stdClass $termFromApi): WP_Term
    {
        $localID       = ResourceFromApiHelper::getLocalID($termFromApi->id, $this);
        $localTaxonomy = $this->getName();

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

        $hookName = 'Municipio/Content/ResourceFromApi/ConvertRestApiTermToWPTerm';
        $term     = apply_filters($hookName, $term, $termFromApi, $localTaxonomy);

        wp_cache_add($term->term_id, $term, 'terms');

        return $term;
    }
}
