<?php

namespace Municipio\Content\ResourceFromApi\Modifiers;

use Municipio\Content\ResourceFromApi\ResourceRegistryInterface;
use Municipio\Content\ResourceFromApi\ResourceType;
use Municipio\Content\ResourceFromApi\Taxonomy\TaxonomyResourceRequest;
use WP_Term_Query;

class ModifyTermsPreQuery
{
    private ResourceRegistryInterface $resourceRegistry;

    public function __construct(ResourceRegistryInterface $resourceRegistry)
    {
        $this->resourceRegistry = $resourceRegistry;
    }

    public function handle(?array $terms, WP_Term_Query $termQuery): ?array
    {
        $queryVars = $termQuery->query_vars;

        if (!isset($queryVars['taxonomy'])) {
            return $terms;
        }

        $cacheKey = ModifiersHelper::getTermQueryCacheKey($queryVars);
        $foundInCache = ModifiersHelper::getTermQueryResultFromCache($cacheKey, $queryVars['taxonomy'][0]);

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
                
                $objectResource = ModifiersHelper::getResourceFromPostId($objectId);

                if( empty($objectResource) ) {
                    return null;
                }

                return (int)str_replace($objectResource->getResourceID(), '', (string)absint($objectId));

            }, $queryVars['object_ids']);

            array_filter($queryVars['object_ids']);
        }

        $collection = TaxonomyResourceRequest::getCollection(reset($matchingResources), $queryVars);

        ModifiersHelper::addTermQueryResultToCache($cacheKey, $collection, $queryVars['taxonomy'][0]);

        return $collection;
    }
}
