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
        add_filter('get_terms', [$this, 'modifyGetTerms'], 10, 4);
        add_filter('get_object_terms', [$this, 'modifyGetObjectTerms'], 10, 4);
        add_filter('Municipio/Archive/getTaxonomyFilters/option/value', [$this, 'modifyGetTaxonomyFiltersOptionValue'], 10, 3);
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

    public function modifyGetTerms(array $terms, $taxonomy, array $queryVars, WP_Term_Query $termQuery): array
    {
        if (!isset($queryVars['taxonomy'])) {
            return $terms;
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

        return $collection;
    }

    public function modifyGetObjectTerms(array $terms, array $objectIds, array $taxonomies, array $queryVars): array
    {
        if (!isset($queryVars['taxonomy'])) {
            return $terms;
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

        return $collection;
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
