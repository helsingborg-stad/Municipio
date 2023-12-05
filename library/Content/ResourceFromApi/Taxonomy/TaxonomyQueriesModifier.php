<?php

namespace Municipio\Content\ResourceFromApi\Taxonomy;

use Municipio\Content\ResourceFromApi\QueriesModifierInterface;
use Municipio\Content\ResourceFromApi\ResourceInterface;
use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;
use WP_Taxonomy;
use WP_Term;
use WP_Term_Query;

class TaxonomyQueriesModifier implements QueriesModifierInterface
{
    private ResourceRegistryInterface $resourceRegistry;

    public function __construct(ResourceRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    public function addHooks(): void
    {
        add_filter('terms_pre_query', [$this, 'modifyGetTerms'], 10, 2);
        add_filter('get_object_terms', [$this, 'modifyGetObjectTerms'], 10, 4);
        add_filter('Municipio/Archive/getTaxonomyFilters/option/value', [$this, 'modifyGetTaxonomyFiltersOptionValue'], 10, 3);
        add_action('clean_object_term_cache', [$this, 'cleanObjectTermCache'], 10, 2);
    }

    public function cleanObjectTermCache($object_ids, $object_type) {
        $resources = $this->resourceRegistry->getByType(ResourceType::POST_TYPE);
        $matchingResources = array_filter($resources, fn ($resource) => $resource->getName() === $object_type);

        if(empty($matchingResources)) {
            return;
        }

        $postType = get_post_type_object($object_type); 
        $postTypeTaxonomies = $postType->taxonomies;

        foreach($postTypeTaxonomies as $taxonomyname => $taxonomy) {
            $cacheGroup = 'termQueryRemoteResourceResults-' . $taxonomyname;
            wp_cache_flush_group($cacheGroup);
        }

    }

    public function modifyGetTaxonomyFiltersOptionValue(string $value, WP_Term $option, WP_Taxonomy $taxonomy): string
    {
        $resources = $this->resourceRegistry->getByType(ResourceType::TAXONOMY);
        $matchingResources = array_filter($resources, fn ($resource) => $resource->getName() === $taxonomy->name);

        if (empty($matchingResources)) {
            return $value;
        }

        return $option->term_id;
    }

    public function modifyPreGetTerms(WP_Term_Query $termQuery) {

        if( !isset($termQuery->query_vars['taxonomy'][0])) {
            return;
        }

        $resources = $this->resourceRegistry->getByType(ResourceType::TAXONOMY);
        $matchingResources = array_filter($resources, fn ($resource) => $resource->getName() === $termQuery->query_vars['taxonomy'][0]);
        
        if (empty($matchingResources)) {
            return;
        }

        // Set suppress filters to false
        $termQuery->query_vars['suppress_filters'] = false;
    }

    private function getFromCache(string $cacheKey, string $taxonomy): ?array
    {
        $cacheGroup = 'termQueryRemoteResourceResults-' . $taxonomy;
        $foundInCache = wp_cache_get($cacheKey, $cacheGroup);

        if ($foundInCache) {
            return $foundInCache;
        }

        return null;
    }

    private function addToCache(string $cacheKey, array $collection, string $taxonomy): void
    {
        $cacheGroup = 'termQueryRemoteResourceResults-' . $taxonomy;
        wp_cache_add($cacheKey, $collection, $cacheGroup);
    }

    private function getCacheKey(array $queryVars):string {
        return md5(json_encode($queryVars));
    }

    public function modifyGetTerms(?array $terms, WP_Term_Query $termQuery): ?array
    {
        $queryVars = $termQuery->query_vars;

        if (!isset($queryVars['taxonomy'])) {
            return $terms;
        }

        $cacheKey = $this->getCacheKey($queryVars);
        $foundInCache = $this->getFromCache($cacheKey, $queryVars['taxonomy'][0]);

        if ($foundInCache) {
            return $foundInCache;
        }

        $resources = $this->resourceRegistry->getByType(ResourceType::TAXONOMY);
        $matchingResources = array_filter($resources, fn ($resource) => $resource->getName() === $queryVars['taxonomy'][0]);

        if(empty($matchingResources)) {
            return $terms;
        }

        if( isset($queryVars['object_ids']) && !empty($queryVars['object_ids']) ) {
            $queryVars['object_ids'] = array_map(function($objectId){
                
                $objectResource = $this->getResourceFromObjectId($objectId);

                if( empty($objectResource) ) {
                    return null;
                }

                return (int)str_replace($objectResource->getResourceID(), '', (string)absint($objectId));

            }, $queryVars['object_ids']);

            array_filter($queryVars['object_ids']);
        }

        $collection = TaxonomyResourceRequest::getCollection(reset($matchingResources), $queryVars);

        $this->addToCache($cacheKey, $collection, $queryVars['taxonomy'][0]);

        return $collection;
    }

    public function modifyGetObjectTerms($terms, array $objectIds, array $taxonomies, array $queryVars)
    {   
        $terms = !is_array($terms) ? [] : $terms;

        if (!isset($queryVars['taxonomy'])) {
            return $terms;
        }

        $cacheKey = $this->getCacheKey($queryVars);
        $foundInCache = $this->getFromCache($cacheKey, $queryVars['taxonomy'][0]);

        if ($foundInCache) {
            return $foundInCache;
        }

        $resources = $this->resourceRegistry->getByType(ResourceType::TAXONOMY);
        $matchingResources = array_filter($resources, fn ($resource) => $resource->getName() === $queryVars['taxonomy'][0]);

        if(empty($matchingResources)) {
            return $terms;
        }

        if( isset($queryVars['object_ids']) && !empty($queryVars['object_ids']) ) {
            $queryVars['object_ids'] = array_map(function($objectId){
                
                $objectResource = $this->getResourceFromObjectId($objectId);

                if( empty($objectResource) ) {
                    return null;
                }

                return (int)str_replace($objectResource->getResourceID(), '', (string)absint($objectId));

            }, $queryVars['object_ids']);

            array_filter($queryVars['object_ids']);
        }

        $collection = TaxonomyResourceRequest::getCollection(reset($matchingResources), $queryVars);

        if( $collection !== null ) {
            $terms = $collection;
        }

        $this->addToCache($cacheKey, $terms, $queryVars['taxonomy'][0]);

        return $terms;
    }

    private function getResourceFromTermId($termId):?object {

        if( $termId > -1 ) {
            return null;
        }

        $registeredTaxonomies = $this->resourceRegistry->getByType(ResourceType::TAXONOMY);
        
        if(empty($registeredTaxonomies)) {
            return null;
        }

        foreach($registeredTaxonomies as $registeredTaxonomy) {
            $needle = (string)$registeredTaxonomy->getResourceID();
            $haystack = (string)absint($termId);

            if( str_starts_with($haystack, $needle) ) {
                return $registeredTaxonomy;
            }
        }

        return null;
    }
    
    private function getResourceFromObjectId(int $objectId):?ResourceInterface {

        if( $objectId > -1 ) {
            return null;
        }

        $registeredPostTypes = $this->resourceRegistry->getByType(ResourceType::POST_TYPE);
        
        if(empty($registeredPostTypes)) {
            return null;
        }

        foreach($registeredPostTypes as $registeredPostType) {
            $needle = (string)$registeredPostType->getResourceID();
            $haystack = (string)absint($objectId);

            if( str_starts_with($haystack, $needle) ) {
                return $registeredPostType;
            }
        }

        return null;
    }
}
