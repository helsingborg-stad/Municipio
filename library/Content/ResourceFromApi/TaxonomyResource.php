<?php

namespace Municipio\Content\ResourceFromApi;

use Municipio\Helper\ResourceFromApiHelper;
use Municipio\Helper\RestRequestHelper;
use Municipio\Helper\WPTermQueryToRestParamsConverter;
use stdClass;
use WP_Term;

class TaxonomyResource extends Resource
{
    public function getType(): string
    {
        return ResourceType::TAXONOMY;
    }

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

    private function getCollectionUrl(?array $queryArgs = null): ?string
    {
        $url = $this->getBaseUrl();

        $restParams = !empty($queryArgs)
            ? '?' . WPTermQueryToRestParamsConverter::convertToRestParamsString($queryArgs)
            : '';

        return $url . $restParams;
    }

    private function getSingleUrl($id): ?string
    {

        $collectionUrl = $this->getCollectionUrl();

        if (is_numeric($id)) {
            return trailingslashit($collectionUrl) . $id;
        }

        return "{$collectionUrl}/?slug={$id}";
    }

    private function convertRestApiTermToWPTerm(stdClass $termFromApi): WP_Term
    {
        $localID = ResourceFromApiHelper::getLocalID($termFromApi->id, $this);
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
        $term = apply_filters($hookName, $term, $termFromApi, $localTaxonomy);

        wp_cache_add($term->term_id, $term, 'terms');

        return $term;
    }
}
